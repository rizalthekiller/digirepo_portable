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
                    'cat' => 'Mahasiswa: Akun & Verifikasi',
                    'q' => 'Bagaimana cara mendaftar akun untuk Mahasiswa?',
                    'a' => 'Gunakan menu "Register" dan pilih peran Mahasiswa. Pastikan Anda memasukkan NIM dengan benar dan menggunakan email institusi aktif untuk mempercepat proses verifikasi oleh Admin.'
                ],
                [
                    'cat' => 'Mahasiswa: Syarat Unggah Mandiri',
                    'q' => 'Apa saja syarat dokumen untuk unggah mandiri?',
                    'a' => 'Dokumen harus berupa satu file PDF utuh (mulai dari halaman judul, lembar pengesahan, hingga lampiran). Lembar pengesahan wajib sudah ditandatangani oleh dosen pembimbing dan penguji (bisa berupa hasil scan). Ukuran file maksimal 20MB.'
                ],
                [
                    'cat' => 'Mahasiswa: Revisi & Sertifikat',
                    'q' => 'Bagaimana jika pengajuan saya ditolak, dan bagaimana cara dapat sertifikat?',
                    'a' => 'Jika ditolak, baca catatan dari Admin di Dashboard Anda, perbaiki dokumen, dan unggah ulang. Jika disetujui (Approved), Anda bisa langsung mencetak Sertifikat Bukti Penyerahan Mandiri (Syarat Bebas Pustaka) dari Dashboard.'
                ],
                [
                    'cat' => 'Dosen: Eksplorasi Karya',
                    'q' => 'Bagaimana cara Dosen mencari skripsi mahasiswa bimbingan?',
                    'a' => 'Bapak/Ibu Dosen dapat menggunakan fitur "Pencarian Lanjut" di menu Jelajah. Cukup ketikkan nama mahasiswa, atau saring pencarian berdasarkan nama Bapak/Ibu di kolom Dosen Pembimbing.'
                ],
                [
                    'cat' => 'Dosen: Akses Dokumen',
                    'q' => 'Apakah Dosen dapat mengunduh semua file karya ilmiah?',
                    'a' => 'Ya. Dengan login menggunakan akun Dosen, Bapak/Ibu memiliki hak istimewa (hak akses penuh) untuk membaca dan mengunduh berkas skripsi (Full Text) mahasiswa tanpa batasan.'
                ],
                [
                    'cat' => 'Guest: Akses Peneliti Luar',
                    'q' => 'Saya peneliti dari kampus lain, bagaimana cara mencari referensi di sini?',
                    'a' => 'Silakan mendaftar akun dengan memilih role "Guest". Anda diwajibkan menggunakan Nomor Induk Kependudukan (NIK) KTP atau nomor Paspor sebagai identitas dan melengkapi isian Asal Instansi.'
                ],
                [
                    'cat' => 'Guest: Batasan Akses',
                    'q' => 'Apakah akun Guest bisa mengunduh file skripsi utuh (Full Text)?',
                    'a' => 'Tergantung kebijakan institusi dan masa embargo dokumen. Biasanya, publik dan Guest selalu dapat membaca Abstrak dan Bab awal secara bebas. Beberapa Full Text mungkin dikunci dan memerlukan izin khusus dari perpustakaan untuk alasan hak cipta.'
                ],
                [
                    'cat' => 'Umum: Aturan Sitasi',
                    'q' => 'Apa aturan mengutip/sitasi karya dari DigiRepo?',
                    'a' => 'Seluruh dokumen di sini dilindungi oleh hak cipta. Jika Anda mengutip, wajib mencantumkan sitasi resmi (Nama Penulis, Tahun Lulus, Judul, Institusi, dan URL dokumen di sistem kami). Plagiarisme sangat dilarang.'
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
