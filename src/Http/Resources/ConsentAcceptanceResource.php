<?php

namespace Reno\Forms\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Reno\Forms\Interfaces\Forms\FormInterface;
use Reno\Forms\Models\ConsentAcceptance;

class ConsentAcceptanceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var ConsentAcceptance $acceptance */
        $acceptance = $this->resource;

        return [
            'id' => $acceptance->getKey(),
            'consent_id' => $acceptance->consent_id,
            'submission_id' => $acceptance->submission_id,
            'deleted_by' => $acceptance->deleted_by,
            'name' => $acceptance->name,
            'phone' => $acceptance->phone,
            'email' => $acceptance->email,
            'title' => $acceptance->title,
            'form_title' => $this->resolveFormTitle($acceptance),
            'user' => $this->resolveUser($acceptance),
            'deleted_by_user' => $this->resolveDeletedByUser($acceptance),
            'ip' => $acceptance->ip,
            'user_agent' => $acceptance->user_agent,
            'created_at' => $acceptance->created_at,
            'updated_at' => $acceptance->updated_at,
            'deleted_at' => $acceptance->deleted_at,
        ];
    }

    private function resolveFormTitle(ConsentAcceptance $acceptance): string
    {
        $formClass = $acceptance->submission?->form?->class;
        if (!is_string($formClass) || $formClass === '' || !class_exists($formClass)) {
            return '';
        }

        /** @var mixed $form */
        $form = app($formClass);
        if (!$form instanceof FormInterface) {
            return '';
        }

        return $form->getTitle();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function resolveUser(ConsentAcceptance $acceptance): ?array
    {
        $user = $acceptance->submission?->user;
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

    /**
     * @return array<string, mixed>|null
     */
    private function resolveDeletedByUser(ConsentAcceptance $acceptance): ?array
    {
        $user = $acceptance->deletedByUser;
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
