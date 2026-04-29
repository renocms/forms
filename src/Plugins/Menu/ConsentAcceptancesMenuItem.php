<?php

namespace Reno\Forms\Plugins\Menu;

use Reno\Cms\Plugins\Menu\AbstractTopMenuItem;

class ConsentAcceptancesMenuItem extends AbstractTopMenuItem
{
    public function getId(): string
    {
        return 'forms-consents';
    }

    public function getLabel(): string
    {
        return __('forms::forms.form_consents');
    }

    public function getPath(): ?string
    {
        return 'forms/consents';
    }

    public function getParentId(): ?string
    {
        return 'forms';
    }

    public function getOrder(): int
    {
        return 20;
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
