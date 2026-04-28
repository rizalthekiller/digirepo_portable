<x-mail::message>
@php
    $docLabel = $thesis->user->isDosen() ? 'Karya Tulis' : 'Skripsi';
    $siteName = \App\Models\Setting::get('site_name', config('app.name'));
@endphp
# Halo, {{ $thesis->user->name }}!

Selamat! {{ $docLabel }} Anda telah disetujui oleh admin.

**Detail {{ $docLabel }}:**
- **Judul:** {{ $thesis->title }}
- **Nomor Sertifikat:** {{ $thesis->certificate_number }}

Anda sekarang dapat mengunduh Sertifikat Penyerahan {{ $docLabel }} secara resmi melalui tautan di bawah ini:

<x-mail::button :url="route('theses.certificate', $thesis->id)">
Download Sertifikat
</x-mail::button>

Silakan simpan sertifikat ini sebagai bukti sah penyerahan karya ilmiah Anda ke repositori perpustakaan.

Terima kasih,<br>
Tim Repositori {{ $siteName }}
</x-mail::message>
