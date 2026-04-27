@extends('layouts.app')

@section('title', 'Tanya Jawab (F.A.Q) - DigiRepo')

@section('styles')
<style>
    .faq-header { 
        padding: 120px 0 80px; 
        background: var(--dark); 
        color: white;
        text-align: center; 
    }
    .faq-header h1 { color: white; }
    .faq-header p { color: rgba(255,255,255,0.7) !important; }
    
    .accordion-item { border: none; margin-bottom: 20px; border-radius: 20px !important; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.02); }
    .accordion-button { padding: 25px; font-weight: 700; color: #1e293b; background: white; }
    .accordion-button:not(.collapsed) { background: #f8fafc; color: #3b82f6; box-shadow: none; }
    .accordion-button::after { background-size: 1rem; }
    .accordion-body { padding: 0 25px 25px; color: #64748b; line-height: 1.8; }
</style>
@endsection

@section('content')
<section class="faq-header">
    <div class="container animate-fade-in">
        <div class="badge bg-primary bg-opacity-25 text-white px-3 py-2 rounded-pill mb-3 fw-bold">Pusat Bantuan</div>
        <h1 class="display-4 fw-800 mb-3">Pertanyaan Umum</h1>
        <p class="mx-auto" style="max-width: 600px;">Temukan jawaban cepat untuk pertanyaan yang sering ditanyakan mengenai sistem repositori digital kami.</p>
    </div>
</section>

<section class="container py-5" style="max-width: 800px;">
    <div class="accordion animate-fade-in" id="faqAccordion">
        @foreach([
            [
                'q' => 'Bagaimana cara mendaftar akun di DigiRepo?',
                'a' => 'Anda dapat mendaftar dengan mengklik tombol "Daftar" di pojok kanan atas. Pastikan menggunakan email institusi yang valid dan mengisi data diri sesuai dengan identitas mahasiswa Anda.'
            ],
            [
                'q' => 'Berapa lama proses verifikasi akun?',
                'a' => 'Tim Admin perpustakaan biasanya melakukan verifikasi akun dalam waktu 1x24 jam pada hari kerja. Anda akan menerima email notifikasi setelah akun Anda disetujui.'
            ],
            [
                'q' => 'Apa saja syarat file skripsi yang boleh diunggah?',
                'a' => 'File harus dalam format PDF dengan ukuran maksimal 20MB. Pastikan file PDF sudah mencakup seluruh bagian skripsi (dari cover hingga lampiran) yang telah disahkan.'
            ],
            [
                'q' => 'Bagaimana jika pengajuan skripsi saya ditolak?',
                'a' => 'Jika ditolak, Anda akan melihat alasan penolakan di Dashboard. Anda dapat memperbaiki data atau file yang diminta, lalu melakukan unggah ulang (revisi) pada menu yang tersedia.'
            ],
            [
                'q' => 'Bagaimana cara mendapatkan sertifikat bukti penyerahan?',
                'a' => 'Sertifikat akan dikirimkan secara otomatis ke email Anda segera setelah Admin menyetujui pengajuan skripsi Anda. Anda juga dapat mengunduhnya kapan saja melalui Dashboard mahasiswa.'
            ],
            [
                'q' => 'Apakah publik bisa membaca full text skripsi?',
                'a' => 'Ya, publik dapat membaca full text skripsi yang telah disetujui melalui fitur "Jelajahi". Namun, untuk mengunduh file PDF secara utuh, beberapa institusi mungkin mewajibkan Anda untuk login terlebih dahulu.'
            ]
        ] as $index => $faq)
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button {{ $index != 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}">
                    {{ $faq['q'] }}
                </button>
            </h2>
            <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    {{ $faq['a'] }}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="text-center mt-5 pt-4 animate-fade-in">
        <p class="text-secondary">Masih punya pertanyaan lain?</p>
        <a href="mailto:support@repo.id" class="btn btn-primary rounded-pill px-5">Hubungi Kami</a>
    </div>
</section>
@endsection
