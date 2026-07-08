<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Location;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class PublicTicketController extends Controller
{
    public function index()
    {
        $locations = Location::orderBy('name', 'asc')->get();
        $categories = Category::orderBy('name', 'asc')->get();

        return view('landing', compact('locations', 'categories'));
    }

    public function store(Request $request)
    {
        // Rate limiter: 1 request per menit per IP
        $key = 'kirim-tiket:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            return back()
                ->withInput()
                ->withErrors(['limit' => "Mohon tunggu $seconds detik lagi sebelum mengirim laporan baru."]);
        }

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_hp' => 'required|numeric',
            'lokasi' => 'required|string',
            'topik_bantuan' => 'required|string',
            'deskripsi_umum_masalah' => 'required|string|max:255',
            'penjelasan_lengkap' => 'required|string',
            'gambar.*' => 'nullable|image|mimes:jpeg,png,gif|max:5120',
        ]);

        RateLimiter::hit($key, 60);

        // Upload gambar (multiple)
        $gambarPaths = [];
        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $file) {
                if ($file->isValid()) {
                    $gambarPath = $file->store('laporan-gambar', 'public');
                    $gambarPaths[] = $gambarPath;
                }
            }
        }
        $validated['gambar'] = !empty($gambarPaths) ? json_encode($gambarPaths) : null;

        $ticket = Ticket::create($validated);

        $this->sendEmailNotification($ticket);
        $this->sendWhatsAppNotification($ticket);

        return redirect()->route('laporan.sukses', ['uuid' => $ticket->uuid]);
    }

    private function sendEmailNotification($ticket)
    {
        $linkTracking = route('laporan.cek', [], true) . '?uuid=' . $ticket->uuid;
        $subject = "[IT Helpdesk] Laporan Anda Telah Diterima - #{$ticket->no_tiket}";
        
        $htmlBody = "
        <html>
            <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <div style='max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; padding: 20px;'>
                    <h2 style='color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px;'>
                        Laporan IT Helpdesk PTPN IV
                    </h2>
                    
                    <p>Halo <strong>{$ticket->nama_lengkap}</strong>,</p>
                    
                    <p>Terima kasih telah melaporkan kendala IT kepada kami. Laporan Anda telah berhasil diterima dan sedang kami proses.</p>
                    
                    <div style='background-color: #ecf0f1; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                        <p style='margin: 5px 0;'><strong>Nomor Tiket:</strong> {$ticket->no_tiket}</p>
                        <p style='margin: 5px 0;'><strong>Kategori:</strong> {$ticket->topik_bantuan}</p>
                        <p style='margin: 5px 0;'><strong>Status:</strong> Dalam Antrian</p>
                        <p style='margin: 5px 0;'><strong>Tanggal:</strong> " . $ticket->created_at->format('d M Y H:i') . "</p>
                    </div>
                    
                    <p style='margin-top: 20px; margin-bottom: 10px;'><strong>Cara Melacak Laporan Anda:</strong></p>
                    <p>Kunjungi link berikut untuk melihat status terbaru laporan Anda:</p>
                    <p><a href='{$linkTracking}' style='background-color: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Lacak Laporan Anda</a></p>
                    
                    <p style='margin-top: 20px; color: #7f8c8d; font-size: 12px;'>
                        Atau copy-paste link ini di browser: <br>
                        {$linkTracking}
                    </p>
                    
                    <hr style='border: none; border-top: 1px solid #ddd; margin: 30px 0;'>
                    
                    <p style='color: #7f8c8d; font-size: 12px;'>
                        <strong>Catatan:</strong> Tim kami akan merespons laporan Anda dalam waktu yang telah ditentukan. 
                        Jika ada pertanyaan, hubungi kami melalui WhatsApp atau email.
                    </p>
                </div>
            </body>
        </html>
        ";
        
        try {
            Mail::html($htmlBody, function ($message) use ($ticket, $subject) {
                $message->to($ticket->email)
                    ->subject($subject)
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });
        } catch (\Exception $e) {
            \Log::error('Email gagal dikirim untuk Ticket #' . $ticket->no_tiket . ': ' . $e->getMessage());
        }
    }

    private function sendWhatsAppNotification($ticket)
    {
        \App\Jobs\SendWhatsAppNotification::dispatch($ticket);
    }

    public function success($uuid)
    {
        $ticket = Ticket::where('uuid', $uuid)->firstOrFail();
        return view('sukses', compact('ticket'));
    }
    
    public function cek(Request $request)
    {
        $uuid = $request->query('uuid');
        if (!$uuid) return redirect()->route('home');

        $ticket = Ticket::where('uuid', $uuid)->firstOrFail();

        $isExpired = $this->isTicketExpired($ticket);
        $adminSudahJawab = $ticket->comments()->whereNotNull('user_id')->exists();

        return view('lacak', compact('ticket', 'isExpired', 'adminSudahJawab'));
    }

    public function reply(Request $request, $uuid)
    {
        $request->validate([
            'isi_pesan' => 'required|string',
            'attachments.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $ticket = Ticket::where('uuid', $uuid)->firstOrFail();

        if ($this->isTicketExpired($ticket)) {
             return back()->withErrors(['status' => 'Tiket ini sudah ditutup permanen dan tidak bisa dibalas lagi.']);
        }

        $adminSudahJawab = $ticket->comments()->whereNotNull('user_id')->exists();
        if (!$adminSudahJawab) {
            return back()->withErrors(['status' => 'Mohon tunggu balasan dari Admin terlebih dahulu sebelum mengirim pesan.']);
        }

        // Upload lampiran
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if ($file->isValid()) {
                    $attachmentPaths[] = $file->store('comment-attachments', 'public');
                }
            }
        }

        $ticket->comments()->create([
            'user_id' => null,
            'content' => $request->isi_pesan,
            'attachments' => !empty($attachmentPaths) ? $attachmentPaths : null,
        ]);

        if ($ticket->status !== 'Open') {
            $ticket->update(['status' => 'Open', 'reopened_at' => now(), 'solved_at' => null]);
        } else {
            $ticket->update(['reopened_at' => now()]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            $html = view('partials.chat_single', ['comment' => $ticket->comments()->latest()->first(), 'ticket' => $ticket])->render();
            return response()->json(['success' => true, 'message' => 'Pesan terkirim!', 'html' => $html]);
        }

        return back()->with('success', 'Pesan terkirim!');
    }

    public function chatHistory(Request $request) 
    {
        $uuid = $request->query('uuid');
        if (!$uuid) return response()->json(['error' => 'UUID required'], 400);

        $ticket = Ticket::where('uuid', $uuid)->firstOrFail();
        
        $html = view('partials.chat_history', compact('ticket'))->render();
        $adminSudahJawab = $ticket->comments()->whereNotNull('user_id')->exists();
        
        return response()->json([
            'html' => $html,
            'status' => $ticket->status,
            'adminSudahJawab' => $adminSudahJawab,
            'isExpired' => $this->isTicketExpired($ticket)
        ]);
    }

    private function isTicketExpired($ticket)
    {
        $days = 5;
        if ($ticket->resolutionSla) {
            $days = (int) $ticket->resolutionSla->response_days;
        } elseif ($ticket->sla) {
             $days = (int) $ticket->sla->response_days;
        }

        return $ticket->created_at->copy()->addDays($days)->isPast() || $ticket->status === 'Closed';
    }

    public function uploadTrixImage(Request $request) 
    {
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('trix-attachments', 'public');
            return response()->json(['url' => asset('storage/' . $path)]);
        }
        
        return response()->json(['error' => 'No file uploaded'], 400);
    }
}