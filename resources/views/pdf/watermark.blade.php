<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            width: 210mm;
            height: 297mm;
            position: relative;
        }
        .watermark-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%); /* Hapus rotasi */
            width: 100%;
            text-align: center;
            opacity: 0.08; /* Opacity sangat tipis agar profesional */
            z-index: -1;
        }
        .watermark-logo {
            width: 140mm; /* Ukuran besar namun tetap proporsional */
            filter: grayscale(100%); /* Efek abu-abu agar tidak tabrakan warna */
        }
        .footer-stamp {
            position: absolute;
            bottom: 40px;
            right: 50px;
            text-align: right;
            opacity: 0.4;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        .footer-text {
            font-family: 'Helvetica', sans-serif;
            font-size: 8px; /* Sedikit dikecilkan agar lebih elegan */
            color: #444;
            line-height: 1.6; /* Ditambah agar tidak tumpang tindih */
            display: block;
            margin-bottom: 1px;
        }
        .verified-badge {
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 9px;
            margin-bottom: 4px; /* Jarak ekstra setelah judul */
        }
    </style>
</head>
<body>
    <div class="watermark-container">
        @if($logo)
            <img src="{{ $logo }}" class="watermark-logo">
        @else
            <div style="font-family: Helvetica; font-size: 60px; font-weight: bold; opacity: 0.5;">{{ config('app.name') }}</div>
        @endif
    </div>

    <div class="footer-stamp">
        <div class="footer-text verified-badge">Digital Repository Verified</div>
        <div class="footer-text">ID: {{ strtoupper($hash) }}</div>
        <div class="footer-text">Arsip Digital Perpustakaan - {{ date('d M Y') }}</div>
    </div>
</body>
</html>
