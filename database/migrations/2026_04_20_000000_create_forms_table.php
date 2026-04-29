<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Reno\Cms\Helpers\TablePrefixHelper;
use Reno\Cms\Models\Resource;
use Reno\Forms\Models\Consent;
use Reno\Forms\Models\Form;
use Reno\Forms\Models\FormSubmission;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(TablePrefixHelper::table('forms'), function (Blueprint $table): void {
            $table->id();
            $table->string('class')->unique();
            $table->timestamps();
        });

        Schema::create(TablePrefixHelper::table('consents'), function (Blueprint $table): void {
            $table->id();
            $table->string('class')->unique();
            $table->timestamps();
        });

        Schema::create(TablePrefixHelper::table('form_submissions'), function (Blueprint $table): void {
            $table->id();
            $table->foreignId('form_id')->constrained(Form::getTableName())->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('resource_id')->nullable()->constrained(Resource::getTableName())->restrictOnDelete();
            $table->json('payload')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('ip', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index('form_id');
            $table->index('user_id');
            $table->index('resource_id');
        });

        Schema::create(TablePrefixHelper::table('consent_acceptances'), function (Blueprint $table): void {
            $table->id();
            $table->foreignId('consent_id')->constrained(Consent::getTableName())->restrictOnDelete();
            $table->foreignId('submission_id')->constrained(FormSubmission::getTableName())->restrictOnDelete();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->string('name')->nullable();
            $table->string('phone', 64)->nullable();
            $table->string('email')->nullable();
            $table->string('title');
            $table->string('ip', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('consent_id');
            $table->index('submission_id');
            $table->index('deleted_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(TablePrefixHelper::table('consent_acceptances'));
        Schema::dropIfExists(TablePrefixHelper::table('form_submissions'));
        Schema::dropIfExists(TablePrefixHelper::table('consents'));
        Schema::dropIfExists(TablePrefixHelper::table('forms'));
    }
};
