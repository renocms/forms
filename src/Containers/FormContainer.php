<?php

namespace Reno\Forms\Containers;

use Reno\Forms\Interfaces\Forms\FormInterface;
use Reno\Forms\Interfaces\Repositories\ConsentsRepositoryInterface;

class FormContainer
{
    public function __construct(
        private readonly int $id,
        private readonly FormInterface $form,
    )
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }

    /**
     * @return array<ConsentContainer>
     */
    public function getConsents(): array
    {
        $consents = [];
        /** @var ConsentsRepositoryInterface $consentsRepository */
        $consentsRepository = app(ConsentsRepositoryInterface::class);

        foreach ($this->form->getConsentClasses() as $consentClass) {
            $consents[] = $consentsRepository->findByClassName($consentClass);
        }

        return $consents;
    }
}
