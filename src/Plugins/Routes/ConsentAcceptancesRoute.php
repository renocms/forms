<?php

namespace Reno\Forms\Plugins\Routes;

use Reno\Cms\Interfaces\JavascriptRouteInterface;

class ConsentAcceptancesRoute implements JavascriptRouteInterface
{
    public function getName(): string
    {
        return 'forms-consents';
    }

    public function getPath(): string
    {
        return 'forms/consents';
    }

    public function getJsModule(): string
    {
        return '/js/reno/forms/build/components/forms/ConsentAcceptancesPage.js';
    }

    public function getMeta(): array
    {
        return [];
    }
}
