<?php

namespace Reno\Forms\Forms;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Reno\Forms\Interfaces\Forms\FormInterface;
use Reno\Forms\Mail\DefaultMail;
use Reno\Forms\Models\FormSubmission;

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

    public function submitUsing(FormSubmission $submission): callable
    {
        return function (FormSubmission $submission): void {
            Mail::send(new DefaultMail($this, $submission));
        };
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function beforeResponse(JsonResponse $response, FormSubmission $submission, array $payload, Request $request): JsonResponse
    {
        return $response;
    }

    public function getMailViewName(): ?string
    {
        return null;
    }
}
