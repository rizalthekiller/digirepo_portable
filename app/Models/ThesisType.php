<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ThesisType extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($type) {
            if (!$type->slug) {
                $type->slug = Str::slug($type->name);
            }
        });
    }
}
