<?php

namespace Reno\Forms\Interfaces\Services;

use Reno\Forms\Models\ConsentAcceptance;

interface ConsentServiceInterface
{
    public function create(
        int $consentId,
        int $submissionId,
        string $title,
        ?string $name,
        ?string $phone,
        ?string $email,
        ?string $ip,
        ?string $userAgent,
    ): ConsentAcceptance;

    public function delete(int $acceptanceId, ?int $deletedBy): ?ConsentAcceptance;
}
