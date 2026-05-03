<?php

namespace Reno\Forms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Reno\Cms\Helpers\TablePrefixHelper;

class FormSubmission extends Model
{
    protected $fillable = [
        'form_id',
        'user_id',
        'resource_id',
        'payload',
        'name',
        'email',
        'phone',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public static function getTableName(): string
    {
        return TablePrefixHelper::table('form_submissions');
    }

    public function getTable(): string
    {
        return static::getTableName();
    }

    public function getId(): int
    {
        return (int) $this->getKey();
    }

    /**
     * @return array<string, mixed>
     */
    public function getPayload(): array
    {
        return is_array($this->payload) ? $this->payload : [];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function user(): BelongsTo
    {
        /** @var class-string<EloquentModel> $userModelClass */
        $userModelClass = (string) config('auth.providers.users.model', \App\Models\User::class);

        return $this->belongsTo($userModelClass, 'user_id');
    }

    public function consentAcceptances(): HasMany
    {
        return $this->hasMany(ConsentAcceptance::class, 'submission_id');
    }
}
