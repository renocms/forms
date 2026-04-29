<?php

namespace Reno\Forms\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Reno\Cms\Services\ClassesDiscoverer;
use Reno\Forms\Containers\FormContainer;
use Reno\Forms\Events\FormsRegistering;
use Reno\Forms\Interfaces\Forms\FormInterface;
use Reno\Forms\Interfaces\Repositories\FormsRepositoryInterface;
use Reno\Forms\Models\Form;

class FormsRepository implements FormsRepositoryInterface
{
    /**
     * @var array<class-string<FormInterface>, FormInterface>|null
     */
    private static ?array $rawFormsCache = null;

    /**
     * @var array<class-string<FormInterface>, FormContainer>
     */
    private static array $formsByClassCache = [];

    /**
     * @var array<string, Form>|null
     */
    private static ?array $formModelsCache = null;

    /**
     * @var array<int, class-string<FormInterface>>|null
     */
    private static ?array $formClassesByIdCache = null;

    /**
     * @var array<string, int>
     */
    private static array $nameToIdCache = [];

    public function __construct(
        private readonly ClassesDiscoverer $classesDiscoverer,
    )
    {
    }

    /**
     * @return Collection<int, FormContainer>
     */
    public function getAll(): Collection
    {
        $result = collect();

        foreach (array_keys($this->getRawForms()) as $className) {
            $container = $this->resolveFormByClassName($className);
            $result->put($container->getId(), $container);
        }

        return $result;
    }

    public function findById(int $id): FormContainer
    {
        $this->initFormModelsCache();

        if (!isset(self::$formClassesByIdCache[$id])) {
            throw new \RuntimeException("Form with ID {$id} not found.");
        }

        return $this->resolveFormByClassName(self::$formClassesByIdCache[$id]);
    }

    public function findByName(string $name): FormContainer
    {
        if (isset(self::$nameToIdCache[$name])) {
            return $this->findById(self::$nameToIdCache[$name]);
        }

        foreach ($this->getRawForms() as $className => $form) {
            if ($form->getName() !== $name) {
                continue;
            }

            return $this->resolveFormByClassName($className);
        }

        throw new \RuntimeException("Form '{$name}' not found.");
    }

    public function clearCache(): void
    {
        self::$rawFormsCache = null;
        self::$formsByClassCache = [];
        self::$formModelsCache = null;
        self::$formClassesByIdCache = null;
        self::$nameToIdCache = [];
    }

    /**
     * @return array<class-string<FormInterface>, FormInterface>
     */
    private function getRawForms(): array
    {
        if (self::$rawFormsCache !== null) {
            return self::$rawFormsCache;
        }

        $lock = Cache::lock('forms:sync');

        if (!$lock->block(5)) {
            throw new \RuntimeException('Forms synchronization is locked');
        }

        try {
            $event = new FormsRegistering();
            Event::dispatch($event);

            if ((bool) config('forms.discover_forms', true)) {
                $path = (string) config('forms.forms_path', app_path('Reno/Forms'));
                foreach ($this->classesDiscoverer->discover($path) as $className) {
                    if (!is_subclass_of($className, FormInterface::class)) {
                        continue;
                    }

                    /** @var FormInterface $form */
                    $form = app($className);
                    $event->addForm($form);
                }
            }

            $formsByClass = [];
            foreach ($event->getForms() as $form) {
                $formsByClass[$form::class] = $form;
            }

            ksort($formsByClass);
            self::$rawFormsCache = $formsByClass;
        } finally {
            $lock->release();
        }

        return self::$rawFormsCache;
    }

    private function resolveFormByClassName(string $className): FormContainer
    {
        if (isset(self::$formsByClassCache[$className])) {
            return self::$formsByClassCache[$className];
        }

        $rawForms = $this->getRawForms();

        if (!isset($rawForms[$className])) {
            throw new \RuntimeException("Form '{$className}' not found.");
        }

        $form = $rawForms[$className];
        $model = $this->getModelForForm($form);
        $container = new FormContainer(
            id: (int) $model->getKey(),
            form: $form,
        );

        if (self::$formModelsCache === null) {
            self::$formModelsCache = [];
        }
        if (self::$formClassesByIdCache === null) {
            self::$formClassesByIdCache = [];
        }

        self::$formsByClassCache[$className] = $container;
        self::$formModelsCache[$className] = $model;
        self::$formClassesByIdCache[$container->getId()] = $className;
        self::$nameToIdCache[$container->getForm()->getName()] = $container->getId();

        return $container;
    }

    private function initFormModelsCache(): void
    {
        if (self::$formModelsCache !== null) {
            return;
        }

        $models = Form::query()->get();

        self::$formModelsCache = $models->keyBy('class')->all();
        self::$formClassesByIdCache = $models
            ->keyBy('id')
            ->map(fn (Form $model) => $model->class)
            ->toArray();
    }

    private function getModelForForm(FormInterface $form): Form
    {
        $this->initFormModelsCache();

        $class = $form::class;

        if (isset(self::$formModelsCache[$class])) {
            return self::$formModelsCache[$class];
        }

        $model = Form::query()->updateOrCreate([
            'class' => $class,
        ]);

        if (self::$formModelsCache === null) {
            self::$formModelsCache = [];
        }
        if (self::$formClassesByIdCache === null) {
            self::$formClassesByIdCache = [];
        }

        self::$formModelsCache[$class] = $model;
        self::$formClassesByIdCache[$model->getKey()] = $class;

        return $model;
    }
}
