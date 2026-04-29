<?php

namespace Reno\Forms\Forms;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Reno\Forms\Interfaces\Forms\FormInterface;
use Reno\Forms\Interfaces\Forms\FormSubmissionInterface;

abstract class AbstractForm implements FormInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getValidationRules(): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
    public function getMessages(): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
    public function getAttributes(): array
    {
        return [];
    }

    /**
     * @return array<class-string>
     */
    public function getConsentClasses(): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
    public function getFieldsMapping(): array
    {
        return [];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function beforeSubmit(array $payload, Request $request): array
    {
        $user = $request->user();
        if (!$user) {
            return $payload;
        }

        if (!isset($payload['name']) && isset($user->name)) {
            $payload['name'] = $user->name;
        }

        if (!isset($payload['email']) && isset($user->email)) {
            $payload['email'] = $user->email;
        }

        return $payload;
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function submitUsing(FormSubmissionInterface $submission, array $payload, Request $request): callable|null
    {
        return null;
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function beforeResponse(JsonResponse $response, FormSubmissionInterface $submission, array $payload, Request $request): JsonResponse
    {
        return $response;
    }
}
