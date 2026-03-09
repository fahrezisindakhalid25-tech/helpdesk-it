<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Support\TicketSecurity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ticket;
    protected $customMessage;

    public function __construct(Ticket $ticket, $customMessage = null)
    {
        $this->ticket = $ticket;
        $this->customMessage = $customMessage;
    }

    public function handle(): void
    {
        $ticket = $this->ticket;
        $token = config('services.fonnte.token');

        if (! $token) {
            Log::warning('WA_API_TOKEN tidak ditemukan di ENV saat memproses Job Ticket #' . $ticket->no_tiket);
            return;
        }

        $phone = $this->formatPhoneNumber($ticket->no_hp);

        if ($this->customMessage) {
            $message = $this->customMessage;
        } else {
            $linkTracking = TicketSecurity::trackingUrl($ticket);
            $message = "IT Helpdesk PTPN IV\n\n"
                . "Halo {$ticket->nama_lengkap},\n\n"
                . "Laporan Anda telah diterima!\n\n"
                . "Nomor Tiket: {$ticket->no_tiket}\n"
                . "Kategori: {$ticket->topik_bantuan}\n"
                . "Status: Dalam Antrian\n\n"
                . "Lacak laporan Anda di:\n"
                . "{$linkTracking}\n\n"
                . "Tim kami akan segera menghubungi Anda.";
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
            ]);

            Log::info('WhatsApp Job Result #' . $ticket->no_tiket . ': ' . $response->body());

            if (! $response->successful()) {
                Log::error('WhatsApp Job Failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp Job Error for Ticket #' . $ticket->no_tiket . ': ' . $e->getMessage());
        }
    }

    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        return $phone;
    }
}
