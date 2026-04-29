<?php

namespace Reno\Forms\Interfaces\Services;

interface FormSubmissionContextProviderInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getContext(): array;

    /**
     * @return array<string, string>
     */
    public function getFieldsMapping(): array;
}
