<?php

namespace Reno\Forms\Interfaces\Forms;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Reno\Forms\Models\FormSubmission;

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
     * Отправка формы выполняется в job после сохранения submission.
     */
    public function submitUsing(FormSubmission $submission): callable;

    /**
     * @param array<string, mixed> $payload
     */
    public function beforeResponse(JsonResponse $response, FormSubmission $submission, array $payload, Request $request): JsonResponse;

    public function getMailViewName(): ?string;
}
