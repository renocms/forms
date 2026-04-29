<?php

use Illuminate\Contracts\Support\Htmlable;
use Reno\Forms\Interfaces\Services\FormRendererInterface;

if (!function_exists('renderForm')) {
    function renderForm(string $formName, array $data = []): Htmlable
    {
        /** @var FormRendererInterface $renderer */
        $renderer = app(FormRendererInterface::class);
        return $renderer->render($formName, $data);
    }
}
