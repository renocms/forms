<?php

namespace Reno\Forms\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Reno\Forms\Http\Requests\FormSubmissionsIndexRequest;
use Reno\Forms\Http\Resources\FormSubmissionResource;
use Reno\Forms\Models\FormSubmission;

class FormSubmissionController
{
    public function index(FormSubmissionsIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $perPage = (int) ($validated['per_page'] ?? 20);

        $query = FormSubmission::query()
            ->with(['form', 'user'])
            ->latest();

        return FormSubmissionResource::collection(
            $query->paginate($perPage)
        )->response();
    }

    public function show(int $id): JsonResponse
    {
        $submission = FormSubmission::query()
            ->with([
                'form',
                'user',
                'consentAcceptances' => function ($query): void {
                    $query->withTrashed()->with(['consent', 'submission.form', 'submission.user'])->latest();
                },
            ])
            ->find($id);

        if (!$submission) {
            return response()->json([
                'message' => __('forms::forms.submission_not_found'),
            ], 404);
        }

        return (new FormSubmissionResource($submission))->response();
    }
}
