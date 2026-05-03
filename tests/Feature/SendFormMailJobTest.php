<?php

namespace Reno\Forms\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Reno\Forms\Containers\FormContainer;
use Reno\Forms\Forms\AbstractForm;
use Reno\Forms\Jobs\SendFormMailJob;
use Reno\Forms\Interfaces\Repositories\FormsRepositoryInterface;
use Reno\Forms\Mail\DefaultMail;
use Reno\Forms\Models\Form;
use Reno\Forms\Models\FormSubmission;
use Tests\TestCase;

class SendFormMailJobTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_sends_default_mail_via_job(): void
    {
        Mail::fake();
        config(['queue.default' => 'sync']);

        $form = new JobTestForm();
        $formModel = Form::query()->create([
            'class' => $form::class,
        ]);

        $submission = FormSubmission::query()->create([
            'form_id' => (int) $formModel->getKey(),
            'payload' => [
                'name' => 'Иван',
                'phone' => '+79990000000',
            ],
            'name' => 'Иван',
            'phone' => '+79990000000',
        ]);

        $formsRepositoryMock = Mockery::mock(FormsRepositoryInterface::class);
        $formsRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with((int) $formModel->getKey())
            ->andReturn(new FormContainer((int) $formModel->getKey(), $form));

        $this->app->instance(FormsRepositoryInterface::class, $formsRepositoryMock);

        SendFormMailJob::dispatch((int) $formModel->getKey(), (int) $submission->getKey());

        Mail::assertSent(DefaultMail::class, 1);
    }
}

class JobTestForm extends AbstractForm
{
    public function getName(): string
    {
        return 'job-test-form';
    }

    public function getTitle(): string
    {
        return 'Job Test Form';
    }

    public function getViewName(): string
    {
        return 'forms::default-form';
    }

    /**
     * @return array<string, string>
     */
    public function getFieldsMapping(): array
    {
        return [
            'name' => 'Имя',
            'phone' => 'Телефон',
        ];
    }
}
