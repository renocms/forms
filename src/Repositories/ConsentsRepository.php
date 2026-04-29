<?php

namespace Reno\Forms\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Reno\Cms\Services\ClassesDiscoverer;
use Reno\Forms\Containers\ConsentContainer;
use Reno\Forms\Events\ConsentsRegistering;
use Reno\Forms\Interfaces\Consents\ConsentInterface;
use Reno\Forms\Interfaces\Repositories\ConsentsRepositoryInterface;
use Reno\Forms\Models\Consent;

class ConsentsRepository implements ConsentsRepositoryInterface
{
    /**
     * @var array<class-string<ConsentInterface>, ConsentInterface>|null
     */
    private static ?array $rawConsentsCache = null;

    /**
     * @var array<class-string<ConsentInterface>, ConsentContainer>
     */
    private static array $consentsByClassCache = [];

    /**
     * @var array<string, Consent>|null
     */
    private static ?array $consentModelsCache = null;

    /**
     * @var array<int, class-string<ConsentInterface>>|null
     */
    private static ?array $consentClassesByIdCache = null;

    public function __construct(
        private readonly ClassesDiscoverer $classesDiscoverer,
    )
    {
    }

    /**
     * @return Collection<int, ConsentContainer>
     */
    public function getAll(): Collection
    {
        $result = collect();

        foreach (array_keys($this->getRawConsents()) as $className) {
            $container = $this->resolveConsentByClassName($className);
            $result->put($container->getId(), $container);
        }

        return $result;
    }

    public function findById(int $id): ConsentContainer
    {
        $this->initConsentModelsCache();

        if (!isset(self::$consentClassesByIdCache[$id])) {
            throw new \RuntimeException("Consent with ID {$id} not found.");
        }

        return $this->resolveConsentByClassName(self::$consentClassesByIdCache[$id]);
    }

    public function findByClassName(string $className): ConsentContainer
    {
        return $this->resolveConsentByClassName($className);
    }

    public function clearCache(): void
    {
        self::$rawConsentsCache = null;
        self::$consentsByClassCache = [];
        self::$consentModelsCache = null;
        self::$consentClassesByIdCache = null;
    }

    /**
     * @return array<class-string<ConsentInterface>, ConsentInterface>
     */
    private function getRawConsents(): array
    {
        if (self::$rawConsentsCache !== null) {
            return self::$rawConsentsCache;
        }

        $lock = Cache::lock('consents:sync');

        if (!$lock->block(5)) {
            throw new \RuntimeException('Consents synchronization is locked');
        }

        try {
            $event = new ConsentsRegistering();
            Event::dispatch($event);

            if ((bool) config('forms.discover_consents', true)) {
                $path = (string) config('forms.consents_path', app_path('Reno/Consents'));
                foreach ($this->classesDiscoverer->discover($path) as $className) {
                    if (!is_subclass_of($className, ConsentInterface::class)) {
                        continue;
                    }

                    /** @var ConsentInterface $consent */
                    $consent = app($className);
                    $event->addConsent($consent);
                }
            }

            $consentsByClass = [];
            foreach ($event->getConsents() as $consent) {
                $consentsByClass[$consent::class] = $consent;
            }

            ksort($consentsByClass);
            self::$rawConsentsCache = $consentsByClass;
        } finally {
            $lock->release();
        }

        return self::$rawConsentsCache;
    }

    private function resolveConsentByClassName(string $className): ConsentContainer
    {
        if (isset(self::$consentsByClassCache[$className])) {
            return self::$consentsByClassCache[$className];
        }

        $rawConsents = $this->getRawConsents();

        if (!isset($rawConsents[$className])) {
            throw new \RuntimeException("Consent '{$className}' not found.");
        }

        $consent = $rawConsents[$className];
        $model = $this->getModelForConsent($consent);
        $container = new ConsentContainer(
            id: (int) $model->getKey(),
            consent: $consent,
        );

        if (self::$consentModelsCache === null) {
            self::$consentModelsCache = [];
        }
        if (self::$consentClassesByIdCache === null) {
            self::$consentClassesByIdCache = [];
        }

        self::$consentsByClassCache[$className] = $container;
        self::$consentModelsCache[$className] = $model;
        self::$consentClassesByIdCache[$container->getId()] = $className;

        return $container;
    }

    private function initConsentModelsCache(): void
    {
        if (self::$consentModelsCache !== null) {
            return;
        }

        $models = Consent::query()->get();

        self::$consentModelsCache = $models->keyBy('class')->all();
        self::$consentClassesByIdCache = $models
            ->keyBy('id')
            ->map(fn (Consent $model) => $model->class)
            ->toArray();
    }

    private function getModelForConsent(ConsentInterface $consent): Consent
    {
        $this->initConsentModelsCache();

        $class = $consent::class;

        if (isset(self::$consentModelsCache[$class])) {
            return self::$consentModelsCache[$class];
        }

        $model = Consent::query()->updateOrCreate([
            'class' => $class,
        ]);

        if (self::$consentModelsCache === null) {
            self::$consentModelsCache = [];
        }
        if (self::$consentClassesByIdCache === null) {
            self::$consentClassesByIdCache = [];
        }

        self::$consentModelsCache[$class] = $model;
        self::$consentClassesByIdCache[$model->getKey()] = $class;

        return $model;
    }
}
