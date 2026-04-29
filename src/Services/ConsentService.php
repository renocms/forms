<?php

namespace Reno\Forms\Services;

use Reno\Forms\Interfaces\Services\ConsentServiceInterface;
use Reno\Forms\Models\ConsentAcceptance;

class ConsentService implements ConsentServiceInterface
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
    ): ConsentAcceptance
    {
        return ConsentAcceptance::query()->create([
            'consent_id' => $consentId,
            'submission_id' => $submissionId,
            'title' => $title,
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'ip' => $ip,
            'user_agent' => $userAgent,
        ]);
    }

    public function delete(int $acceptanceId, ?int $deletedBy): ?ConsentAcceptance
    {
        $acceptance = ConsentAcceptance::query()->find($acceptanceId);
        if (!$acceptance) {
            return null;
        }

        $acceptance->deleted_by = $deletedBy;
        $acceptance->save();
        $acceptance->delete();

        return $acceptance;
    }
}
