<?php

namespace Reno\Forms\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Reno\Forms\Http\Requests\ConsentAcceptancesIndexRequest;
use Reno\Forms\Http\Resources\ConsentAcceptanceResource;
use Reno\Forms\Interfaces\Services\ConsentServiceInterface;
use Reno\Forms\Models\ConsentAcceptance;

class ConsentAcceptanceController
{
    public function __construct(
        private readonly ConsentServiceInterface $consentService,
    )
    {
    }

    public function index(ConsentAcceptancesIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $perPage = (int) ($validated['per_page'] ?? 20);

        $query = ConsentAcceptance::query()
            ->withTrashed()
            ->with(['consent', 'submission.form', 'submission.user', 'deletedByUser'])
            ->latest();

        return ConsentAcceptanceResource::collection(
            $query->paginate($perPage)
        )->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $deletedBy = auth()->id();
        $acceptance = $this->consentService->delete(
            $id,
            is_numeric($deletedBy) ? (int) $deletedBy : null,
        );
        if (!$acceptance) {
            return response()->json([
                'message' => __('forms::forms.consent_not_found'),
            ], 404);
        }

        return response()->json([
            'message' => __('forms::forms.consent_revoked'),
        ]);
    }
}
