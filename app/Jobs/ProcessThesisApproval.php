<?php

namespace App\Jobs;

use App\Models\Thesis;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\ThesisApprovedMail;
use Illuminate\Support\Facades\Storage;

class ProcessThesisApproval implements ShouldQueue
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
        // 1. Watermarking (Heavy Task)
        // Watermark legacy main file
        if ($this->thesis->file_path) {
            $this->applyWatermarkToPath($this->thesis->file_path);
        }

        // Watermark all multi-files
        if ($this->thesis->files) {
            foreach ($this->thesis->files as $file) {
                if ($file->file_path) {
                    $this->applyWatermarkToPath($file->file_path);
                }
            }
        }
        
        $this->thesis->update(['is_watermarked' => true]);

        // 2. Generate Certificate PDF (HANYA UNTUK MAHASISWA)
        $pdfPath = null;
        if ($this->thesis->user->role === 'mahasiswa') {
            $pdfPath = \App\Services\WatermarkGenerator::generateCertificatePdf($this->thesis);
        }

        // 3. Send Email Notification (Mahasiswa dapat lampiran, Dosen tidak)
        Mail::to($this->thesis->user->email)->send(new ThesisApprovedMail($this->thesis, $pdfPath));

        // 4. Cleanup temporary PDF certificate
        if ($pdfPath && file_exists($pdfPath)) {
            @unlink($pdfPath);
        }

        // 5. Mark as complete
        $this->thesis->update(['delivery_status' => 'sent']);
    }

    protected function applyWatermarkToPath($relativePath)
    {
        $originalPath = storage_path('app/public/' . $relativePath);
        if (!file_exists($originalPath)) return;

        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) mkdir($tempDir, 0777, true);
        
        $tempPath = $tempDir . '/' . str_replace('.pdf', '_watermarked.pdf', basename($relativePath));
        
        // Deteksi QPDF Portabel di dalam project
        $portableQpdf = base_path('bin/qpdf/qpdf.exe');
        $qpdfCmd = file_exists($portableQpdf) ? "\"{$portableQpdf}\"" : "qpdf";

        try {
            // STEP 1: Generate a high-quality watermark layer using DomPDF (HTML to PDF)
            $watermarkLayer = \App\Services\WatermarkGenerator::generateWatermarkLayer($this->thesis->verification_hash);

            if (!$watermarkLayer || !file_exists($watermarkLayer)) {
                \Log::warning("Watermark generation failed for thesis {$this->thesis->id}");
                return;
            }

            // STEP 2: Use FPDI to merge the watermark layer with the original PDF
            $success = \App\Services\WatermarkGenerator::merge($originalPath, $watermarkLayer, $tempPath);

            if ($success && file_exists($tempPath)) {
                @unlink($originalPath);
                @rename($tempPath, $originalPath);
            } else {
                \Log::error("FPDI Merge failed for Path: {$relativePath}");
            }

            // STEP 3: Cleanup temporary watermark layer
            if ($watermarkLayer && file_exists($watermarkLayer)) {
                @unlink($watermarkLayer);
            }
        } catch (\Exception $e) {
            \Log::error("Watermark generation failed: " . $e->getMessage());
        }
    }

    protected function generateCertificate()
    {
        // Implementasi DomPDF untuk generate sertifikat
        return "fake-pdf-content";
    }
}
