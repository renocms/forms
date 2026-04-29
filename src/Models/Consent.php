<?php

namespace Reno\Forms\Models;

use Illuminate\Database\Eloquent\Model;
use Reno\Cms\Helpers\TablePrefixHelper;

class Consent extends Model
{
    protected $fillable = [
        'class',
    ];

    public static function getTableName(): string
    {
        return TablePrefixHelper::table('consents');
    }

    public function getTable(): string
    {
        return static::getTableName();
    }
}
