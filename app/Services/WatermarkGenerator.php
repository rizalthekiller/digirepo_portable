<?php

namespace App\Services;

use App\Models\Setting;
use Dompdf\Dompdf;
use Dompdf\Options;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Log;

class WatermarkGenerator
{
    /**
     * Generate a watermark PDF from HTML using DomPDF.
     * 
     * @param string $hash Unique verification hash
     * @return string Path to the generated watermark PDF
     */
    public static function generateWatermarkLayer($hash)
    {
        // Prioritaskan logo spesifik yang diminta user
        $logoPath = 'assets/logo.png';
        $absLogoPath = public_path($logoPath);
        
        // Fallback ke setting jika file aset tidak ditemukan
        if (!file_exists($absLogoPath)) {
            $dbLogo = Setting::get('site_watermark_path') ?: Setting::get('site_logo_path');
            if ($dbLogo) {
                $absLogoPath = public_path(ltrim($dbLogo, '/'));
            }
        }
        
        // Base64 encode logo for DomPDF
        $logoBase64 = null;
        if (file_exists($absLogoPath)) {
            $type = pathinfo($absLogoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($absLogoPath);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $html = view('pdf.watermark', [
            'logo' => $logoBase64,
            'hash' => $hash
        ])->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) mkdir($tempDir, 0777, true);

        $outputPath = $tempDir . '/layer_' . time() . '.pdf';
        file_put_contents($outputPath, $dompdf->output());

        return $outputPath;
    }

    /**
     * Apply watermark to an existing PDF using FPDI.
     * 
     * @param string $sourcePath Path to the original PDF
     * @param string $watermarkPath Path to the watermark layer PDF
     * @param string $outputPath Path to save the result
     * @return bool Success status
     */
    public static function merge($sourcePath, $watermarkPath, $outputPath)
    {
        try {
            // First, use QPDF to ensure source is version 1.4 (FPDI free compatibility)
            // This prevents "This PDF document probably uses a compression technique which is not supported by the free parser"
            $portableQpdf = base_path('bin/qpdf/qpdf.exe');
            $qpdfCmd = file_exists($portableQpdf) ? "\"{$portableQpdf}\"" : "qpdf";
            
            $compatSource = $sourcePath . '.compat.pdf';
            $compatWatermark = $watermarkPath . '.compat.pdf';
            
            exec("{$qpdfCmd} --object-streams=disable \"{$sourcePath}\" \"{$compatSource}\"");
            exec("{$qpdfCmd} --object-streams=disable \"{$watermarkPath}\" \"{$compatWatermark}\"");

            $pdf = new Fpdi();
            
            $sourceToUse = file_exists($compatSource) ? $compatSource : $sourcePath;
            $watermarkToUse = file_exists($compatWatermark) ? $compatWatermark : $watermarkPath;

            $pageCount = $pdf->setSourceFile($sourceToUse);
            $pdf->setSourceFile($watermarkToUse);
            $watermarkTemplate = $pdf->importPage(1);

            for ($i = 1; $i <= $pageCount; $i++) {
                $pdf->setSourceFile($sourceToUse);
                $template = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($template);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($template);
                
                // Overlay watermark template
                $pdf->useTemplate($watermarkTemplate, 0, 0, $size['width'], $size['height']);
            }

            $pdf->Output('F', $outputPath);

            // Cleanup
            if (file_exists($compatSource)) unlink($compatSource);
            if (file_exists($compatWatermark)) unlink($compatWatermark);

            return true;
        } catch (\Exception $e) {
            Log::error("FPDI Merge Error: " . $e->getMessage());
            return false;
        }
    }
    /**
     * Generate Certificate PDF for a thesis.
     * 
     * @param \App\Models\Thesis $thesis
     * @return string Path to the generated certificate PDF
     */
    public static function generateCertificatePdf($thesis)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); // Penting untuk QR Code API
        $options->set('dpi', 96); // Mengunci skala agar 1:1 dengan browser

        $dompdf = new Dompdf($options);
        
        // Render view ke HTML
        $html = view('admin.certificates.print', [
            'thesis' => $thesis,
            'is_pdf' => true
        ])->render();
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) mkdir($tempDir, 0777, true);

        $filename = 'Sertifikat_' . str_replace('/', '_', $thesis->certificate_number) . '.pdf';
        $outputPath = $tempDir . '/' . $filename;
        
        file_put_contents($outputPath, $dompdf->output());

        return $outputPath;
    }
}
