<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Reno\Cms\Helpers\TablePrefixHelper;
use Reno\Cms\Models\Context;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(TablePrefixHelper::table('form_submissions'), function (Blueprint $table): void {
            $table->foreignId('context_id')
                ->nullable()
                ->after('resource_id')
                ->constrained(Context::getTableName())
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table(TablePrefixHelper::table('form_submissions'), function (Blueprint $table): void {
            $table->dropForeign(['context_id']);
            $table->dropColumn('context_id');
        });
    }
};
