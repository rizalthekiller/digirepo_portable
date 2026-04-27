<?php

namespace App\Jobs;

use App\Models\Thesis;
use App\Models\User;
use App\Models\ThesisFile;
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
    protected $filesData; // Array: [['temp' => ..., 'final' => ..., 'label' => ...], ...]
    protected $isResubmission;

    /**
     * Create a new job instance.
     */
    public function __construct(Thesis $thesis, array $filesData, bool $isResubmission = false)
    {
        $this->thesis = $thesis;
        $this->filesData = $filesData;
        $this->isResubmission = $isResubmission;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Hapus file lama jika ini adalah resubmission (opsional, tergantung kebijakan)
            if ($this->isResubmission) {
                $this->thesis->files()->delete();
            }

            $primaryPath = null;

            foreach ($this->filesData as $index => $fileInfo) {
                $tempPath = $fileInfo['temp'];
                $finalPath = $fileInfo['final'];
                $label = $fileInfo['label'];

                if (Storage::disk('local')->exists($tempPath)) {
                    // Ensure directory exists
                    $directory = dirname($finalPath);
                    if (!Storage::disk('public')->exists($directory)) {
                        Storage::disk('public')->makeDirectory($directory);
                    }

                    // Move file
                    $stream = Storage::disk('local')->readStream($tempPath);
                    Storage::disk('public')->writeStream($finalPath, $stream);
                    
                    if (is_resource($stream)) {
                        fclose($stream);
                    }

                    // Save to thesis_files table
                    ThesisFile::create([
                        'thesis_id' => $this->thesis->id,
                        'label' => $label,
                        'file_path' => $finalPath,
                        'is_public' => true,
                        'order' => $index
                    ]);

                    // Set first file as primary path for compatibility
                    if ($index === 0) {
                        $primaryPath = $finalPath;
                    }

                    // Cleanup temp
                    Storage::disk('local')->delete($tempPath);
                }
            }

            // Update thesis primary path
            if ($primaryPath) {
                $this->thesis->update([
                    'file_path' => $primaryPath,
                    'status' => 'pending',
                ]);
            }

            // Trigger Admin Notification
            $admins = User::whereIn('role', ['admin', 'superadmin'])->get();
            Notification::send($admins, new ThesisNotification($this->thesis, $this->isResubmission ? 'resubmitted' : 'submitted'));

        } catch (\Exception $e) {
            \Log::error("Gagal memproses antrean upload skripsi ganda (ID: {$this->thesis->id}): " . $e->getMessage());
            throw $e;
        }
    }
}
