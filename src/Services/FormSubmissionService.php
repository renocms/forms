<?php

namespace Reno\Forms\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Reno\Forms\Jobs\SendFormMailJob;
use Reno\Forms\Interfaces\Repositories\FormsRepositoryInterface;
use Reno\Forms\Interfaces\Services\ConsentServiceInterface;
use Reno\Forms\Interfaces\Services\FormSubmissionContextProviderInterface;
use Reno\Forms\Models\FormSubmission;
use Throwable;

class FormSubmissionService
{
    public function __construct(
        private readonly FormsRepositoryInterface $formsRepository,
        private readonly FormSubmissionContextProviderInterface $contextProvider,
        private readonly ConsentServiceInterface $consentService,
    )
    {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function submit(
        string $formName,
        array $data,
        ?int $userId,
        ?string $ip,
        ?string $userAgent,
    ): JsonResponse
    {
        $request = request();
        $formContainer = $this->formsRepository->findByName($formName);
        $form = $formContainer->getForm();
        $consents = $formContainer->getConsents();

        $consentValidationRules = [];
        $consentValidationMessages = [];
        foreach ($consents as $consentContainer) {
            if ($consentContainer->getConsent()->isRequired()) {
                $consentValidationRules['consent' . $consentContainer->getId()] = ['accepted'];
            } else {
                $consentValidationRules['consent' . $consentContainer->getId()] = ['nullable', 'boolean'];
            }
            $consentValidationMessages['consent' . $consentContainer->getId()] = $consentContainer->getConsent()->getValidationMessage();
        }

        $validation = Validator::make(
            $data,
            array_merge($form->getValidationRules(), $consentValidationRules),
            array_merge($form->getMessages(), $consentValidationMessages),
            $form->getAttributes(),
        );

        if ($validation->fails()) {
            return response()->json([
                'response' => 'fail',
                'fields' => $validation->errors()->toArray(),
            ], 422);
        }

        $payload = $form->beforeSubmit($validation->validated(), $request);
        $context = $this->contextProvider->getContext();
        $payload = array_merge($payload, $context);

        DB::beginTransaction();

        try {
            $submission = FormSubmission::query()->create([
                'form_id' => $formContainer->getId(),
                'user_id' => $userId,
                'resource_id' => Arr::get($context, 'resource_id'),
                'payload' => $payload,
                'name' => Arr::get($payload, 'name'),
                'email' => Arr::get($payload, 'email'),
                'phone' => Arr::get($payload, 'phone'),
                'ip' => $ip,
                'user_agent' => $userAgent,
            ]);

            foreach ($consents as $consentContainer) {
                if (!Arr::get($validation->validated(), 'consent' . $consentContainer->getId())) {
                    continue;
                }

                $this->consentService->create(
                    $consentContainer->getId(),
                    $submission->getId(),
                    $consentContainer->getConsent()->getTitle(),
                    $submission->name,
                    $submission->phone,
                    $submission->email,
                    $ip,
                    $userAgent,
                );
            }
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        DB::commit();

        SendFormMailJob::dispatch($formContainer->getId(), $submission->getId());

        $response = response()->json([
            'response' => 'success',
            'messages' => [__('forms::forms.form_submitted_successfully')],
            'data' => [
                'id' => $submission->getKey(),
            ],
        ]);

        return $form->beforeResponse($response, $submission, $payload, $request);
    }
}
