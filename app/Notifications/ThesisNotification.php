<?php

namespace App\Notifications;

use App\Models\Thesis;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ThesisNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $thesis;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(Thesis $thesis, $type)
    {
        $this->thesis = $thesis;
        $this->type = $type; // 'submitted' or 'approved' or 'rejected'
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Untuk status approved, email dikirim via Mailable manual di Job 
        // agar bisa menyertakan lampiran PDF sertifikat.
        if ($this->type === 'approved') {
            return ['database'];
        }

        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        if ($this->type === 'submitted') {
            return (new MailMessage)
                ->subject('Pengajuan Skripsi Baru: ' . $this->thesis->title)
                ->greeting('Halo Admin,')
                ->line('Mahasiswa ' . $this->thesis->user->name . ' baru saja mengunggah skripsi baru.')
                ->line('Judul: ' . $this->thesis->title)
                ->action('Tinjau Pengajuan', route('admin.queue'))
                ->line('Silakan lakukan verifikasi dokumen tersebut.');
        }

        if ($this->type === 'approved') {
            return (new MailMessage)
                ->subject('Skripsi Anda Telah Disetujui!')
                ->greeting('Selamat ' . $this->thesis->user->name . '!')
                ->line('Skripsi Anda yang berjudul "' . $this->thesis->title . '" telah berhasil diverifikasi dan disetujui oleh Admin Perpustakaan.')
                ->line('Sertifikat keterangan unggah telah diterbitkan.')
                ->action('Download Sertifikat', route('theses.certificate', $this->thesis->id))
                ->line('Terima kasih telah menggunakan layanan DigiRepo.');
        }

        if ($this->type === 'rejected') {
            return (new MailMessage)
                ->subject('Update Status Pengajuan Skripsi')
                ->greeting('Halo ' . $this->thesis->user->name . ',')
                ->line('Mohon maaf, pengajuan skripsi Anda yang berjudul "' . $this->thesis->title . '" perlu diperbaiki atau ditolak.')
                ->line('Silakan cek dashboard untuk melihat alasan penolakan dan lakukan unggah ulang jika diperlukan.')
                ->action('Cek Dashboard', route('dashboard'))
                ->line('Semangat dalam menyelesaikan tugas akhir Anda.');
        }

        return (new MailMessage)
            ->subject('Pemberitahuan Sistem DigiRepo')
            ->line('Terdapat pembaruan pada status pengajuan skripsi Anda.')
            ->action('Buka Dashboard', route('dashboard'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $actionUrl = route('dashboard');
        
        if (in_array($this->type, ['submitted', 'resubmitted'])) {
            $actionUrl = route('admin.queue');
        } elseif (in_array($this->type, ['approved', 'rejected'])) {
            $actionUrl = route('theses.show', $this->thesis->id);
        }

        return [
            'thesis_id' => $this->thesis->id,
            'title' => $this->thesis->title,
            'type' => $this->type,
            'user_name' => $this->thesis->user->name,
            'message' => $this->getMessage(),
            'action_url' => $actionUrl,
        ];
    }

    protected function getMessage()
    {
        switch ($this->type) {
            case 'submitted':
                return "Mahasiswa {$this->thesis->user->name} mengunggah skripsi baru.";
            case 'resubmitted':
                return "Mahasiswa {$this->thesis->user->name} telah mengunggah revisi skripsi.";
            case 'approved':
                return "Skripsi Anda '{$this->thesis->title}' telah disetujui.";
            case 'rejected':
                return "Skripsi Anda '{$this->thesis->title}' perlu diperbaiki.";
            default:
                return "Ada pembaruan pada status skripsi.";
        }
    }
}
