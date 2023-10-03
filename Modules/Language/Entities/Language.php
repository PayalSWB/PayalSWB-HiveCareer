<?php

namespace Modules\Language\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'icon', 'direction'];

    public static function boot()
    {
        parent::boot();

        self::updated(function ($model) {
            forgetCache('default_language');
        });
    }
}
