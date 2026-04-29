<?php

namespace Reno\Forms\Events;

use Reno\Forms\Interfaces\Forms\FormInterface;

class FormsRegistering
{
    /**
     * @var array<FormInterface>
     */
    private array $forms = [];

    public function addForm(FormInterface $form): void
    {
        $this->forms[] = $form;
    }

    /**
     * @return array<FormInterface>
     */
    public function getForms(): array
    {
        return $this->forms;
    }
}
