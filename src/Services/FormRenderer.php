<?php

namespace Reno\Forms\Services;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\Request;
use Reno\Forms\Interfaces\Repositories\FormsRepositoryInterface;
use Reno\Forms\Interfaces\Services\FormRendererInterface;

class FormRenderer implements FormRendererInterface
{
    public function __construct(
        private readonly FormsRepositoryInterface $formsRepository,
        private readonly Request $request,
    )
    {
    }

    public function render(string $formName, array $data = []): Htmlable
    {
        $formContainer = $this->formsRepository->findByName($formName);
        $form = $formContainer->getForm();

        return view($form->getViewName(), array_merge($data, [
            'formContainer' => $formContainer,
            'consents' => $formContainer->getConsents(),
            'submitUrl' => route('sendForm', ['formId' => $form->getName()]),
            'formRequest' => $this->request,
        ]));
    }
}
