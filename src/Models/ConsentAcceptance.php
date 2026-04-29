<?php

namespace Reno\Forms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Reno\Cms\Helpers\TablePrefixHelper;

class ConsentAcceptance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'consent_id',
        'submission_id',
        'deleted_by',
        'name',
        'phone',
        'email',
        'title',
        'ip',
        'user_agent',
    ];

    public static function getTableName(): string
    {
        return TablePrefixHelper::table('consent_acceptances');
    }

    public function getTable(): string
    {
        return static::getTableName();
    }

    public function consent(): BelongsTo
    {
        return $this->belongsTo(Consent::class);
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(FormSubmission::class, 'submission_id');
    }

    public function deletedByUser(): BelongsTo
    {
        /** @var class-string<EloquentModel> $userModelClass */
        $userModelClass = (string) config('auth.providers.users.model', \App\Models\User::class);

        return $this->belongsTo($userModelClass, 'deleted_by');
    }
}
