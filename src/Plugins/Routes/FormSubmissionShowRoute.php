<?php

namespace Reno\Forms\Plugins\Routes;

use Reno\Cms\Interfaces\JavascriptRouteInterface;

class FormSubmissionShowRoute implements JavascriptRouteInterface
{
    public function getName(): string
    {
        return 'forms-submission-show';
    }

    public function getPath(): string
    {
        return 'forms/submissions/:id';
    }

    public function getJsModule(): string
    {
        return '/vendor/reno/forms/build/components/forms/FormSubmissionShowPage.js';
    }

    public function getMeta(): array
    {
        return [];
    }
}
