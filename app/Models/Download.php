<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Download extends Model
{
    protected $fillable = [
        'thesis_id', 'user_id', 'ip_address', 'user_agent'
    ];

    /**
     * Get the thesis that was downloaded.
     */
    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }

    /**
     * Get the user who performed the download.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
