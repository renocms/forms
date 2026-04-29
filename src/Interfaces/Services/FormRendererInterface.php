<?php

namespace Reno\Forms\Interfaces\Services;

use Illuminate\Contracts\Support\Htmlable;

interface FormRendererInterface
{
    public function render(string $formName, array $data = []): Htmlable;
}
