<?php

namespace Reno\Forms\Plugins\Menu;

use Reno\Cms\Plugins\Menu\AbstractTopMenuItem;

class FormSubmissionsMenuItem extends AbstractTopMenuItem
{
    public function getId(): string
    {
        return 'forms-submissions';
    }

    public function getLabel(): string
    {
        return __('forms::forms.form_submissions');
    }

    public function getPath(): ?string
    {
        return 'forms/submissions';
    }

    public function getParentId(): ?string
    {
        return 'forms';
    }

    public function getOrder(): int
    {
        return 10;
    }

    public function getIcon(): ?string
    {
        return null;
    }

    public function isVisible(): bool
    {
        return true;
    }
}
