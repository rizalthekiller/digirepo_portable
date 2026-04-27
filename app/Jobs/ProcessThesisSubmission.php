<?php

namespace App\Jobs;

use App\Models\Thesis;
use App\Models\User;
use App\Notifications\ThesisNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessThesisSubmission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $thesis;
    protected $tempPath;
    protected $finalPath;
    protected $isResubmission;

    /**
     * Create a new job instance.
     */
    public function __construct(Thesis $thesis, string $tempPath, string $finalPath, bool $isResubmission = false)
    {
        $this->thesis = $thesis;
        $this->tempPath = $tempPath;
        $this->finalPath = $finalPath;
        $this->isResubmission = $isResubmission;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // 1. Move file from temp to final destination
            if (Storage::disk('local')->exists($this->tempPath)) {
                // Ensure directory exists in public disk
                $directory = dirname($this->finalPath);
                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory);
                }

                // Copy using streams to save memory
                $stream = Storage::disk('local')->readStream($this->tempPath);
                Storage::disk('public')->writeStream($this->finalPath, $stream);
                
                if (is_resource($stream)) {
                    fclose($stream);
                }

                // Update thesis path and reset status to pending
                $this->thesis->update([
                    'file_path' => $this->finalPath,
                    'status' => 'pending',
                ]);

                // Cleanup temp file
                Storage::disk('local')->delete($this->tempPath);
            }

            // 2. Trigger Admin Notification
            $admins = User::whereIn('role', ['admin', 'superadmin'])->get();
            Notification::send($admins, new ThesisNotification($this->thesis, $this->isResubmission ? 'resubmitted' : 'submitted'));

        } catch (\Exception $e) {
            \Log::error("Gagal memproses antrean upload skripsi (ID: {$this->thesis->id}): " . $e->getMessage());
            // You might want to update thesis status to 'failed' or similar if you had that status
            throw $e; // Retry if failed
        }
    }
}
