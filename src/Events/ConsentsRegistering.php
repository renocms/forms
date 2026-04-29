<?php

namespace Reno\Forms\Events;

use Reno\Forms\Interfaces\Consents\ConsentInterface;

class ConsentsRegistering
{
    /**
     * @var array<ConsentInterface>
     */
    private array $consents = [];

    public function addConsent(ConsentInterface $consent): void
    {
        $this->consents[] = $consent;
    }

    /**
     * @return array<ConsentInterface>
     */
    public function getConsents(): array
    {
        return $this->consents;
    }
}
