<?php

namespace Reno\Forms\Interfaces\Forms;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface FormInterface
{
    public function getName(): string;

    public function getTitle(): string;

    public function getViewName(): string;

    /**
     * @return array<string, mixed>
     */
    public function getValidationRules(): array;

    /**
     * @return array<string, string>
     */
    public function getMessages(): array;

    /**
     * @return array<string, string>
     */
    public function getAttributes(): array;

    /**
     * @return array<class-string>
     */
    public function getConsentClasses(): array;

    /**
     * @return array<string, string>
     */
    public function getFieldsMapping(): array;

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function beforeSubmit(array $payload, Request $request): array;

    /**
     * @param array<string, mixed> $payload
     */
    public function submitUsing(FormSubmissionInterface $submission, array $payload, Request $request): callable|null;

    /**
     * @param array<string, mixed> $payload
     */
    public function beforeResponse(JsonResponse $response, FormSubmissionInterface $submission, array $payload, Request $request): JsonResponse;
}
