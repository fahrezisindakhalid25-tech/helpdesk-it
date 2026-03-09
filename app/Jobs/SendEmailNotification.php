<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Support\TicketSecurity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ticket;
    protected $type;
    protected $content;

    public function __construct(Ticket $ticket, $type = 'new_ticket', $content = null)
    {
        $this->ticket = $ticket;
        $this->type = $type;
        $this->content = $content;
    }

    public function handle(): void
    {
        $ticket = $this->ticket;
        $linkTracking = TicketSecurity::trackingUrl($ticket);

        if ($this->type === 'admin_reply') {
            $subject = "[IT Helpdesk] Balasan Atas Laporan Anda - #{$ticket->no_tiket}";
            $replyContent = TicketSecurity::plainTextFromRichText((string) $this->content);

            $htmlBody = "
            <html>
                <body style='font-family: Arial, sans-serif; color: #333;'>
                    <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                        <div style='background-color: #2c3e50; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0;'>
                            <h2>Balasan dari Admin IT Helpdesk</h2>
                        </div>
                        <div style='background-color: #ecf0f1; padding: 20px; border: 1px solid #bdc3c7;'>
                            <p>Halo <strong>" . e($ticket->nama_lengkap) . "</strong>,</p>
                            <p>Admin kami telah memberikan balasan untuk laporan Anda:</p>
                            <div style='background-color: white; padding: 15px; margin: 15px 0; border-left: 4px solid #3498db;'>
                                <p><strong>Tiket #:</strong> " . e($ticket->no_tiket) . "</p>
                                <p><strong>Kategori:</strong> " . e($ticket->topik_bantuan) . "</p>
                                <hr>
                                <p>" . nl2br(e($replyContent)) . "</p>
                            </div>
                            <p>Silakan cek status laporan dan berikan balasan Anda di link berikut:</p>
                            <p><a href='" . e($linkTracking) . "' style='background-color: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Lihat Detail Laporan</a></p>
                        </div>
                    </div>
                </body>
            </html>";
        } else {
            $subject = "[IT Helpdesk] Tiket Laporan Diterima - #{$ticket->no_tiket}";

            $htmlBody = "
            <html>
                <body style='font-family: Arial, sans-serif; color: #333;'>
                    <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>
                        <div style='text-align: center; padding-bottom: 20px; border-bottom: 2px solid #eee;'>
                            <h2 style='color: #2c3e50; margin: 0;'>IT Helpdesk PTPN IV</h2>
                            <p style='color: #7f8c8d; font-size: 14px;'>Sistem Pelaporan & Tiketing</p>
                        </div>
                        <div style='padding: 20px 0;'>
                            <p>Halo <strong>" . e($ticket->nama_lengkap) . "</strong>,</p>
                            <p>Laporan Anda telah kami terima dengan detail berikut:</p>
                            <table style='width: 100%; border-collapse: collapse; margin-top: 15px;'>
                                <tr><td style='padding: 8px; font-weight: bold;'>Nomor Tiket:</td><td style='padding: 8px; color: #3498db;'>" . e($ticket->no_tiket) . "</td></tr>
                                <tr><td style='padding: 8px; font-weight: bold;'>Kategori:</td><td style='padding: 8px;'>" . e($ticket->topik_bantuan) . "</td></tr>
                                <tr><td style='padding: 8px; font-weight: bold;'>Subjek:</td><td style='padding: 8px;'>" . e($ticket->deskripsi_umum_masalah) . "</td></tr>
                                <tr><td style='padding: 8px; font-weight: bold;'>Waktu Lapor:</td><td style='padding: 8px;'>" . e($ticket->created_at->format('d M Y H:i')) . "</td></tr>
                            </table>
                            <div style='text-align: center; margin-top: 30px;'>
                                <a href='" . e($linkTracking) . "' style='background-color: #27ae60; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Lacak Status Tiket</a>
                            </div>
                        </div>
                    </div>
                </body>
            </html>";
        }

        try {
            Mail::html($htmlBody, function ($message) use ($ticket, $subject) {
                $fromAddress = config('mail.from.address') ?? 'helpdesk@ptpn4.com';
                $fromName = config('mail.from.name') ?? 'IT Helpdesk PTPN IV';

                $message->to($ticket->email)
                    ->subject($subject)
                    ->from($fromAddress, $fromName);
            });
            Log::info("Email notification ($this->type) sent to {$ticket->email} for Ticket #{$ticket->no_tiket}");
        } catch (\Exception $e) {
            Log::error('Email Job Error for Ticket #' . $ticket->no_tiket . ': ' . $e->getMessage());
        }
    }
}
