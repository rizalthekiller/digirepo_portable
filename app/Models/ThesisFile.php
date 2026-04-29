<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ThesisFile extends Model
{
    protected $fillable = [
        'thesis_id', 'label', 'file_path', 'is_public', 'order', 'uuid'
    ];

    /**
     * Get the thesis that owns the file.
     */
    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }

    /**
     * Boot the model and handle file sync.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate UUID
        static::creating(function ($file) {
            if (empty($file->uuid)) {
                $file->uuid = \Illuminate\Support\Str::uuid()->toString();
            }
        });

        // Smart Delete: Hapus file fisik saat record dihapus
        static::deleting(function ($file) {
            if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
