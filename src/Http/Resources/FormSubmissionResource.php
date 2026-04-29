<?php

namespace Reno\Forms\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Reno\Forms\Interfaces\Forms\FormInterface;
use Reno\Forms\Interfaces\Services\FormSubmissionContextProviderInterface;
use Reno\Forms\Models\FormSubmission;

class FormSubmissionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var FormSubmission $submission */
        $submission = $this->resource;

        return [
            'id' => $submission->getKey(),
            'form_id' => $submission->form_id,
            'form_title' => $this->resolveFormTitle($submission),
            'user_id' => $submission->user_id,
            'user' => $this->resolveUser($submission),
            'resource_id' => $submission->resource_id,
            'name' => $submission->name,
            'email' => $submission->email,
            'phone' => $submission->phone,
            'payload' => $submission->payload,
            'fields' => $this->resolveFields($submission),
            'ip' => $submission->ip,
            'user_agent' => $submission->user_agent,
            'created_at' => $submission->created_at,
            'consents' => $this->when(
                $submission->relationLoaded('consentAcceptances'),
                fn (): mixed => ConsentAcceptanceResource::collection($submission->consentAcceptances),
            ),
        ];
    }

    private function resolveFormTitle(FormSubmission $submission): string
    {
        $className = $submission->form?->class;
        if (!is_string($className) || $className === '' || !class_exists($className)) {
            return '';
        }

        /** @var mixed $form */
        $form = app($className);
        if (!$form instanceof FormInterface) {
            return '';
        }

        return $form->getTitle();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function resolveFields(FormSubmission $submission): array
    {
        $payload = is_array($submission->payload) ? $submission->payload : [];
        $mapping = array_merge(
            $this->resolveFormFieldsMapping($submission),
            $this->resolveContextFieldsMapping(),
        );

        $rows = [];
        foreach ($mapping as $key => $label) {
            if (!is_string($key) || $key === '' || !is_string($label) || $label === '') {
                continue;
            }

            if (!Arr::has($payload, $key)) {
                continue;
            }

            $rows[] = [
                'key' => $key,
                'label' => $label,
                'value' => $this->normalizeFieldValue(Arr::get($payload, $key)),
            ];
        }

        return $rows;
    }

    /**
     * @return array<string, string>
     */
    private function resolveFormFieldsMapping(FormSubmission $submission): array
    {
        $className = $submission->form?->class;
        if (!is_string($className) || $className === '' || !class_exists($className)) {
            return [];
        }

        /** @var mixed $form */
        $form = app($className);
        if (!$form instanceof FormInterface) {
            return [];
        }

        return $form->getFieldsMapping();
    }

    /**
     * @return array<string, string>
     */
    private function resolveContextFieldsMapping(): array
    {
        /** @var FormSubmissionContextProviderInterface $contextProvider */
        $contextProvider = app(FormSubmissionContextProviderInterface::class);

        return $contextProvider->getFieldsMapping();
    }

    /**
     * @param mixed $value
     */
    private function normalizeFieldValue(mixed $value): string
    {
        if ($value === null) {
            return '-';
        }

        if (is_bool($value)) {
            return $value ? 'Да' : 'Нет';
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
        }

        return (string) $value;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function resolveUser(FormSubmission $submission): ?array
    {
        $user = $submission->user;
        if (!$user) {
            return null;
        }

        $userId = data_get($user, 'id');
        if (!is_int($userId) && !is_numeric($userId)) {
            return null;
        }

        $adminPrefix = (string) config('cms.admin_prefix', 'admin');

        return [
            'id' => (int) $userId,
            'name' => (string) (data_get($user, 'name') ?: ''),
            'edit_url' => '/' . trim($adminPrefix, '/') . '/users/' . (int) $userId,
        ];
    }
}
