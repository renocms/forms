<?php

namespace Reno\Forms\Interfaces\Consents;

interface ConsentInterface
{
    public function getTitle(): string;

    public function getText(): string;

    public function isRequired(): bool;
}
