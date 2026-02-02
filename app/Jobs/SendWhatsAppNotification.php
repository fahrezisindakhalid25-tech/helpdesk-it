<?php

namespace App\Jobs;

use App\Models\Ticket;
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

    /**
     * Create a new job instance.
     *
     * @param Ticket $ticket
     * @param string|null $customMessage Optional custom message override
     */
    public function __construct(Ticket $ticket, $customMessage = null)
    {
        $this->ticket = $ticket;
        $this->customMessage = $customMessage;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $ticket = $this->ticket;
        $token = config('services.fonnte.token');

        if (!$token) {
            Log::warning('WA_API_TOKEN tidak ditemukan di ENV saat memproses Job Ticket #' . $ticket->no_tiket);
            return;
        }

        // Format nomor ke 62 (Indonesia)
        $phone = $this->formatPhoneNumber($ticket->no_hp);
        // Note: Route inside job might need explicit domain if running from CLI, but usually fine.
        // Better to pass full link if needed, but route() usually works if APP_URL is set.
        
        if ($this->customMessage) {
            // === MODE 1: CUSTOM MESSAGE (E.g. Admin Reply) ===
            $message = $this->customMessage;
        } else {
            // === MODE 2: DEFAULT NEW TICKET MESSAGE ===
            $linkTracking = route('laporan.cek', ['uuid' => $ticket->uuid]); 
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

            if (!$response->successful()) {
                Log::error('WhatsApp Job Failed: ' . $response->body());
                // Optional: throw exception to retry job?
                // throw new \Exception('Fonnte API Error'); 
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp Job Error for Ticket #' . $ticket->no_tiket . ': ' . $e->getMessage());
            // $this->release(60); // Retry after 60s if needed
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
