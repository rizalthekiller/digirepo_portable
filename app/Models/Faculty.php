<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faculty extends Model
{
    protected $fillable = ['name', 'level', 'code'];

    /**
     * Get the departments for the faculty.
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }
}
