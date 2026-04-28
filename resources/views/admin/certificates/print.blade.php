<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan - {{ $thesis->certificate_number }}</title>
    <style>
        @page { 
            size: A4; 
            margin: 1.5cm 2cm 1.5cm 2cm; 
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background: {{ isset($is_pdf) ? '#fff' : '#e2e8f0' }};
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            padding: {{ isset($is_pdf) ? '0' : '40px 0' }};
            margin: 0;
            line-height: 1.5;
        }

        .paper {
            background: white;
            width: {{ isset($is_pdf) ? '100%' : '210mm' }};
            margin: 0 auto;
            padding: {{ isset($is_pdf) ? '0' : '1cm 1.5cm' }};
            position: relative;
            box-shadow: {{ isset($is_pdf) ? 'none' : '0 4px 25px rgba(0,0,0,0.15)' }};
        }

        @media print {
            body { background: none; padding: 0; }
            .paper { 
                box-shadow: none; 
                width: 100%; 
                padding: 0;
                margin: 0;
            }
            .no-print { display: none !important; }
        }

        /* Kop Area */
        .kop-wrapper {
            text-align: center;
            margin-bottom: 20px;
            /* Border dihapus agar tidak double dengan gambar kop */
            border: none;
            padding-bottom: 0;
        }
        .kop-wrapper img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Title Area */
        .title-wrapper {
            text-align: center;
            margin-bottom: 25px;
            margin-top: 10px;
        }
        .title-wrapper h3 {
            margin: 0;
            text-decoration: underline;
            text-transform: uppercase;
            font-size: 18px;
            font-weight: bold;
        }
        .title-wrapper p {
            margin: 5px 0;
            font-weight: bold;
        }

        /* Main Content */
        .content-body {
            text-align: justify;
            margin-bottom: 20px;
            font-size: 15px;
        }
        .info-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .info-table .label { width: 150px; }
        .info-table .colon { width: 20px; }
        .info-table .value { text-transform: uppercase; }

        /* Signature Area (Identical to Digirepo) */
        .sig-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .sig-content {
            text-align: left;
            font-size: 15px;
        }
        .sig-qr { margin: 10px 0; }
        .sig-qr img { width: 80px; height: 80px; }
        .sig-name { font-weight: bold; text-decoration: underline; }

        /* Sembunyikan untuk PDF, Munculkan untuk Browser */
        .no-print {
            display: none;
        }
        @media screen {
            .no-print {
                display: {{ isset($is_pdf) ? 'none' : 'block' }};
                position: fixed;
                bottom: 30px;
                right: 30px;
                background: #0f172a;
                color: white;
                border: none;
                padding: 12px 25px;
                border-radius: 8px;
                font-weight: bold;
                box-shadow: 0 10px 20px rgba(0,0,0,0.2);
                cursor: pointer;
                z-index: 1000;
                text-decoration: none;
            }
        }
    </style>
</head>
<body>

@php
    use App\Models\Setting;
    use Carbon\Carbon;

    $logoPath    = Setting::get('cert_logo_path', 'images/kop_surat.png');
    $sigPrefix   = Setting::get('cert_signatory_prefix', 'Plt.');
    $sigTitle    = Setting::get('cert_signatory_title', 'Kepala UPT. Perpustakaan');
    $sigName     = Setting::get('cert_signatory_name', 'Administrator');
    $sigNip      = Setting::get('cert_signatory_nip', '19800101 200501 1 001');
    $issuedCity  = Setting::get('cert_issued_city', 'Samarinda');

    $bulanIndo = ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $dateObj = $thesis->certificate_date ? Carbon::parse($thesis->certificate_date) : $thesis->updated_at;
    $tanggalSurat = $dateObj->day . ' ' . $bulanIndo[$dateObj->month] . ' ' . $dateObj->year;

    $verifyUrl = url('/verify/' . ($thesis->verification_hash ?? md5($thesis->id)));
    $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($verifyUrl);
    
    // Convert QR to Base64
    $qrBase64 = null;
    try {
        $qrData = file_get_contents($qrApiUrl);
        if ($qrData) {
            $qrBase64 = 'data:image/png;base64,' . base64_encode($qrData);
        }
    } catch (\Exception $e) {
        $qrBase64 = null;
    }
@endphp

<div class="paper">
    <!-- Kode HTML Murni Sesuai Permintaan -->
    <div style="font-family: Arial, Helvetica, sans-serif; color: #000; padding: 0; line-height: 1.5;">
        <div style="text-align: center; margin-bottom: 0;">
            @php
                $fullLogoPath = public_path($logoPath);
                $logoBase64 = null;
                if (file_exists($fullLogoPath) && !is_dir($fullLogoPath)) {
                    $imageData = base64_encode(file_get_contents($fullLogoPath));
                    $mime = mime_content_type($fullLogoPath);
                    $logoBase64 = 'data:' . $mime . ';base64,' . $imageData;
                }
            @endphp

            @if($logoBase64)
                <img src="{{ $logoBase64 }}" style="width: 100%; height: auto; display: block; margin: 0 auto;">
            @else
                <div style="padding: 20px; border: 1px dashed #ccc;">[ LOGO / KOP SURAT ]</div>
            @endif
        </div>

        <div style="padding: 10px 40px;">
            <div style="text-align: center; margin-bottom: 25px; margin-top: 10px;">
                <h3 style="margin: 0; text-decoration: underline; text-transform: uppercase; font-size: 18px; font-weight: bold;">SURAT KETERANGAN</h3>
                <p style="margin: 5px 0; font-weight: bold;">Nomor : {{ $thesis->certificate_number }}</p>
            </div>

            <div style="text-align: justify; margin-bottom: 20px;">
                <p>Kepala Perpustakaan Universitas Islam Negeri Sultan Aji Muhammad Idris (UINSI) Samarinda menerangkan bahwa :</p>
                
                <table style="width: 100%; margin: 20px 0; border: none; border-collapse: collapse;">
                    <tr>
                        <td style="width: 150px; padding: 5px 0; vertical-align: top;">NAMA</td>
                        <td style="width: 20px; vertical-align: top;">:</td>
                        <td style="font-weight: bold; text-transform: uppercase; vertical-align: top;">{{ $thesis->user->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; vertical-align: top;">N.I.M.</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ $thesis->user->nim ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; vertical-align: top;">FAKULTAS</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ $thesis->user->department->faculty->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; vertical-align: top;">PRODI</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ $thesis->user->department->name ?? '-' }}</td>
                    </tr>
                </table>

                <p>Yang bersangkutan telah selesai melakukan unggah Skripsi/Disertasi pada Repositori Perpustakaan Universitas Islam Negeri Sultan Aji Muhammad Idris (UINSI) Samarinda.</p>
                <p>Demikian Surat Keterangan ini diberikan, agar dapat dipergunakan sebagaimana mestinya.</p>
            </div>

            <table style="width: 100%; margin-top: 30px; border: none; border-collapse: collapse;">
                <tr>
                    <td style="width: 55%;"></td>
                    <td style="text-align: left;">
                        <p style="margin: 0;">{{ $issuedCity }}, {{ $tanggalSurat }}</p>
                        <p style="margin: 0;">{{ $sigPrefix }} {{ $sigTitle }},</p>
                        <div style="margin: 10px 0;">
                            @if($qrBase64)
                                <img src="{{ $qrBase64 }}" alt="QR" style="width: 80px; height: 80px;">
                            @else
                                <div style="width: 80px; height: 80px; border: 1px solid #ccc; font-size: 8px; padding: 5px;">QR Error</div>
                            @endif
                        </div>
                        <p style="margin: 0; font-weight: bold; text-decoration: underline;">{{ $sigName }}</p>
                        <p style="margin: 0;">NIP. {{ $sigNip }}</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<button class="no-print" onclick="window.print()">
    CETAK SERTIFIKAT
</button>

@if(!isset($is_pdf))
<script>
    // Hanya auto-print jika ada parameter autoprint=1
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('autoprint')) {
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    }
</script>
@endif

</body>
</html>
