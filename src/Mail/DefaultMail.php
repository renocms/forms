<?php

namespace Reno\Forms\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Arr;
use Reno\Forms\Interfaces\Forms\FormInterface;
use Reno\Forms\Models\FormSubmission;

class DefaultMail extends Mailable
{
    public function __construct(
        protected FormInterface $form,
        protected FormSubmission $submission,
    )
    {
    }

    protected function getRecipientEmails(): array
    {
        return [
            config('mail.from.name') => config('mail.from.address'),
        ];
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->getRecipientEmails(),
            subject: $this->form->getTitle(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->form->getMailViewName() ?? 'forms::default-mail',
            with: [
                'formTitle' => $this->form->getTitle(),
                'fieldRows' => $this->buildFieldRows(),
            ],
        );
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function buildFieldRows(): array
    {
        $flatPayload = Arr::dot($this->submission->getPayload());
        if (empty($flatPayload)) {
            return [];
        }

        $rows = [];
        $fieldsMapping = $this->form->getFieldsMapping();

        foreach ($flatPayload as $key => $value) {
            if (!is_string($key) || $key === '') {
                continue;
            }

            $label = Arr::get($fieldsMapping, $key);
            if (!$label) {
                continue;
            }

            $rows[] = [
                'key' => $key,
                'label' => $label,
                'value' => !empty($value) ? $value : '-',
            ];
        }

        return $rows;
    }
}
