<x-mail::message>
# Halo, {{ $thesis->user->name }}!

Selamat! Skripsi Anda telah disetujui oleh admin.

**Detail Skripsi:**
- **Judul:** {{ $thesis->title }}
- **Nomor Sertifikat:** {{ $thesis->certificate_number }}

Anda sekarang dapat mengunduh Sertifikat Penyerahan Skripsi secara resmi melalui tautan di bawah ini:

<x-mail::button :url="route('theses.certificate', $thesis->id)">
Download Sertifikat
</x-mail::button>

Silakan simpan sertifikat ini sebagai bukti sah penyerahan karya ilmiah Anda ke repositori perpustakaan.

Terima kasih,<br>
Tim Repositori {{ config('app.name') }}
</x-mail::message>
