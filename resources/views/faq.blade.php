@extends('layouts.app')

@section('title', 'Tanya Jawab (F.A.Q) - DigiRepo')

@section('styles')
<style>
    .faq-header { 
        padding: 160px 0 120px; 
        background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%);
        color: white;
        text-align: center; 
        position: relative;
        overflow: hidden;
    }
    .faq-header::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: url('https://www.transparenttextures.com/patterns/cubes.png');
        opacity: 0.1;
    }
    .faq-header h1 { color: white; font-weight: 800; letter-spacing: -1px; }
    .faq-header p { color: rgba(255,255,255,0.7) !important; font-size: 1.1rem; }
    
    .search-faq-container {
        max-width: 700px;
        margin: -45px auto 60px;
        position: relative;
        z-index: 10;
    }
    .search-faq-input {
        background: white;
        border: none;
        padding: 25px 35px;
        border-radius: 40px;
        box-shadow: 0 30px 60px rgba(0,0,0,0.12);
        font-size: 1.1rem;
        width: 100%;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .search-faq-input:focus {
        outline: none;
        transform: translateY(-5px);
        box-shadow: 0 40px 70px rgba(0,0,0,0.15);
    }
    
    .accordion-item { 
        border: none; 
        margin-bottom: 20px; 
        border-radius: 25px !important; 
        overflow: hidden; 
        background: white;
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.03);
    }
    .accordion-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.05);
    }
    .accordion-button { 
        padding: 30px; 
        font-weight: 700; 
        color: #1e293b; 
        background: white;
        font-size: 1.05rem;
    }
    .accordion-button:not(.collapsed) { 
        background: white; 
        color: #3b82f6; 
        box-shadow: none; 
    }
    .accordion-button::after { 
        background-size: 1.25rem;
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .accordion-body { 
        padding: 0 35px 35px; 
        color: #64748b; 
        line-height: 1.9;
        font-size: 0.95rem;
    }
    .badge-category {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 6px 12px;
        border-radius: 50px;
        margin-bottom: 12px;
        display: inline-block;
    }
</style>
@endsection

@section('content')
<section class="faq-header">
    <div class="container animate-fade-in position-relative" style="z-index: 2;">
        <div class="badge bg-white bg-opacity-10 text-white px-4 py-2 rounded-pill mb-4 fw-bold border border-white border-opacity-25" style="backdrop-filter: blur(10px);">
            <i class="fas fa-life-ring me-2"></i> Pusat Bantuan
        </div>
        <h1 class="display-3 mb-3">Ada yang bisa kami bantu?</h1>
        <p class="mx-auto opacity-75" style="max-width: 600px;">Cari jawaban dari pertanyaan yang sering diajukan atau hubungi tim dukungan kami jika Anda tidak menemukan yang Anda cari.</p>
    </div>
</section>

<div class="container">
    <div class="search-faq-container animate-fade-in">
        <div class="position-relative">
            <input type="text" class="search-faq-input" placeholder="Ketik kata kunci pertanyaan Anda..." id="faqSearch">
            <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-5 text-primary opacity-50 fa-lg"></i>
        </div>
    </div>

    <section class="mx-auto py-4" style="max-width: 850px;">
        <div class="accordion animate-fade-in" id="faqAccordion">
            @foreach([
                [
                    'cat' => 'Akun & Pendaftaran',
                    'q' => 'Bagaimana cara mendaftar akun di DigiRepo?',
                    'a' => 'Anda dapat mendaftar dengan mengklik tombol "Daftar" di pojok kanan atas. Pastikan menggunakan email institusi yang valid (misal: @univ.ac.id) dan mengisi data diri sesuai dengan identitas resmi mahasiswa Anda untuk mempercepat proses verifikasi.'
                ],
                [
                    'cat' => 'Verifikasi',
                    'q' => 'Berapa lama proses verifikasi akun?',
                    'a' => 'Tim Admin perpustakaan biasanya melakukan verifikasi akun dalam waktu 1x24 jam pada hari kerja. Pastikan data yang Anda input sudah benar. Anda akan menerima email notifikasi segera setelah akun disetujui untuk digunakan.'
                ],
                [
                    'cat' => 'Teknis Upload',
                    'q' => 'Apa saja syarat file skripsi yang boleh diunggah?',
                    'a' => 'File harus dalam format PDF dengan ukuran maksimal 20MB. Berkas harus merupakan satu kesatuan (full text) mulai dari halaman judul hingga lampiran yang telah ditandatangani dan disahkan oleh pembimbing serta penguji.'
                ],
                [
                    'cat' => 'Proses Penolakan',
                    'q' => 'Bagaimana jika pengajuan skripsi saya ditolak?',
                    'a' => 'Jangan khawatir! Jika ditolak, Admin akan menyertakan alasan spesifik pada Dashboard Anda. Anda dapat memperbaiki kesalahan tersebut (baik pada data input maupun berkas PDF), lalu menekan tombol "Revisi" untuk mengajukan kembali.'
                ],
                [
                    'cat' => 'Sertifikat',
                    'q' => 'Bagaimana cara mendapatkan sertifikat bukti penyerahan?',
                    'a' => 'Sertifikat digital akan diterbitkan dan dikirimkan ke email Anda secara otomatis sesaat setelah status pengajuan berubah menjadi "Approved". Selain di email, sertifikat juga tersimpan permanen di menu Dashboard akun Anda.'
                ],
                [
                    'cat' => 'Akses Publik',
                    'q' => 'Apakah publik bisa membaca full text skripsi?',
                    'a' => 'Ya, sistem kami mendukung keterbukaan informasi akademik. Publik dapat membaca abstrak secara bebas. Untuk membaca full text, pengunjung mungkin perlu melakukan autentikasi atau login sesuai kebijakan akses data institusi.'
                ]
            ] as $index => $faq)
            <div class="accordion-item shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button {{ $index != 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}">
                        <div class="d-flex flex-column align-items-start">
                            <span class="badge-category">{{ $faq['cat'] }}</span>
                            <span>{{ $faq['q'] }}</span>
                        </div>
                    </button>
                </h2>
                <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                    <div class="accordion-body border-top mx-3 pt-4">
                        <div class="p-3 rounded-4 bg-light bg-opacity-50">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="card border-0 rounded-5 p-5 mt-5 text-center animate-fade-in shadow-sm" style="background: #f8fafc; border: 1px solid #e2e8f0 !important;">
            <div class="bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                <i class="fas fa-envelope-open-text text-primary fa-2x"></i>
            </div>
            <h4 class="fw-800 mb-3 text-dark">Belum menemukan jawaban?</h4>
            <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">Tim dukungan teknis kami siap membantu Anda menyelesaikan kendala dalam proses penggunaan sistem repositori.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="mailto:support@repo.id" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-sm">
                    <i class="fas fa-paper-plane me-2"></i> Hubungi Dukungan
                </a>
            </div>
        </div>
    </section>
</div>

<script>
document.getElementById('faqSearch').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let items = document.querySelectorAll('.accordion-item');
    
    items.forEach(function(item) {
        let text = item.textContent.toLowerCase();
        if(text.includes(filter)) {
            item.style.display = "";
        } else {
            item.style.display = "none";
        }
    });
});
</script>
@endsection
