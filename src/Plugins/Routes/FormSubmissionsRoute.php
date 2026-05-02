<?php

namespace Reno\Forms\Plugins\Routes;

use Reno\Cms\Interfaces\JavascriptRouteInterface;

class FormSubmissionsRoute implements JavascriptRouteInterface
{
    public function getName(): string
    {
        return 'forms-submissions';
    }

    public function getPath(): string
    {
        return 'forms/submissions';
    }

    public function getJsModule(): string
    {
        return '/js/reno/forms/build/components/forms/FormSubmissionsListPage.js';
    }

    public function getMeta(): array
    {
        return [];
    }
}
