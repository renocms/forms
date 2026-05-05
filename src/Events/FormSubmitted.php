<?php

namespace Reno\Forms\Events;

use Reno\Forms\Models\FormSubmission;
use Illuminate\Queue\SerializesModels;
use Reno\Forms\Containers\FormContainer;

class FormSubmitted
{
    use SerializesModels;

    public function __construct(
        FormContainer $formContainer,
        FormSubmission $submission,
    )
    {
    }
}
