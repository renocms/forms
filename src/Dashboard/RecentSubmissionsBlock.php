<?php

namespace Reno\Forms\Dashboard;

use Reno\Cms\Interfaces\DashboardBlockInterface;
use Reno\Forms\Interfaces\Forms\FormInterface;
use Reno\Forms\Models\FormSubmission;

class RecentSubmissionsBlock implements DashboardBlockInterface
{
    private const ITEMS_LIMIT = 5;

    public function getJsModule(): string
    {
        return '/js/reno/forms/build/components/dashboard/RecentSubmissionsBlock.js';
    }

    public function getData(): array
    {
        $items = FormSubmission::query()
            ->with(['form'])
            ->latest()
            ->limit(self::ITEMS_LIMIT)
            ->get()
            ->map(function (FormSubmission $submission): array {
                return [
                    'id' => $submission->getId(),
                    'name' => $submission->name,
                    'phone' => $submission->phone,
                    'email' => $submission->email,
                    'form_title' => $this->resolveFormTitle($submission),
                    'created_at' => $submission->created_at?->toIso8601String(),
                ];
            })
            ->values()
            ->all();

        return [
            'items' => $items,
        ];
    }

    public function getSortOrder(): int
    {
        return 25;
    }

    public function isFullWidth(): bool
    {
        return true;
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
}
