<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Thesis extends Model
{
    use HasUuids;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $fillable = [
        'user_id', 'title', 'year', 'abstract', 'keywords', 
        'supervisor_name', 'type', 'file_path', 'status', 
        'rejection_reason', 'was_rejected', 'certificate_number', 
        'verification_hash', 'certificate_content', 'delivery_status', 
        'is_watermarked', 'certificate_date', 'embargo_until',
        'journal_name', 'volume', 'issue', 'pages', 'issn', 'doi',
        'isbn', 'publisher', 'edition'
    ];

    /**
     * Boot the model and handle file sync.
     */
    protected static function boot()
    {
        parent::boot();

        // Smart Delete: Hapus file fisik saat record dihapus
        static::deleting(function ($thesis) {
            if ($thesis->file_path && Storage::disk('public')->exists($thesis->file_path)) {
                Storage::disk('public')->delete($thesis->file_path);
            }
        });

        // Smart Update: Hapus file lama jika file baru diunggah
        static::updating(function ($thesis) {
            if ($thesis->isDirty('file_path')) {
                $oldPath = $thesis->getOriginal('file_path');
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        });
    }

    /**
     * Get the user that owns the thesis.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the files for the thesis.
     */
    public function files()
    {
        return $this->hasMany(ThesisFile::class)->orderBy('order');
    }

    /**
     * Get the downloads for the thesis.
     */
    public function downloads()
    {
        return $this->hasMany(Download::class);
    }
}
