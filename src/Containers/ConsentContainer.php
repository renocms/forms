<?php

namespace Reno\Forms\Containers;

use Reno\Forms\Interfaces\Consents\ConsentInterface;

class ConsentContainer
{
    public function __construct(
        private readonly int $id,
        private readonly ConsentInterface $consent,
    )
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getConsent(): ConsentInterface
    {
        return $this->consent;
    }
}
