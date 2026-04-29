<?php

namespace Reno\Forms\Plugins\Menu;

use Reno\Cms\Plugins\Menu\AbstractTopMenuItem;

class FormsMenuContainer extends AbstractTopMenuItem
{
    public function getId(): string
    {
        return 'forms';
    }

    public function getLabel(): string
    {
        return __('forms::forms.forms_menu');
    }

    public function getPath(): ?string
    {
        return null;
    }

    public function getParentId(): ?string
    {
        return null;
    }

    public function getOrder(): int
    {
        return 30;
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
