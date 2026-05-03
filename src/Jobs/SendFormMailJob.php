<?php

namespace Reno\Forms\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Reno\Forms\Interfaces\Repositories\FormsRepositoryInterface;
use Reno\Forms\Models\FormSubmission;

class SendFormMailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(
        private readonly int $formId,
        private readonly int $submissionId,
    )
    {
    }

    public function handle(FormsRepositoryInterface $formsRepository): void
    {
        $form = $formsRepository->findById($this->formId)->getForm();

        $submission = FormSubmission::findOrFail($this->submissionId);

        $submitHandler = $form->submitUsing($submission);
        $submitHandler($submission);
    }
}
