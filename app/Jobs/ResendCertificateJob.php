<?php

namespace App\Jobs;

use App\Models\Thesis;
use App\Mail\ThesisApprovedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ResendCertificateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $thesis;

    /**
     * Create a new job instance.
     */
    public function __construct(Thesis $thesis)
    {
        $this->thesis = $thesis;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Generate Certificate PDF (HANYA UNTUK MAHASISWA)
        $pdfPath = null;
        if ($this->thesis->user->role === 'mahasiswa') {
            $pdfPath = \App\Services\WatermarkGenerator::generateCertificatePdf($this->thesis);
        }

        // 2. Send Email Notification
        Mail::to($this->thesis->user->email)->send(new ThesisApprovedMail($this->thesis, $pdfPath));

        // 3. Cleanup temporary PDF certificate
        if ($pdfPath && file_exists($pdfPath)) {
            @unlink($pdfPath);
        }

        // 4. Mark as complete
        $this->thesis->update(['delivery_status' => 'sent']);
    }
}
