<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Location;
use App\Models\Ticket;
use App\Support\TicketSecurity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class PublicTicketController extends Controller
{
    public function index(): View
    {
        $locations = Location::orderBy('name', 'asc')->get();
        $categories = Category::orderBy('name', 'asc')->get();

        return view('landing', compact('locations', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $key = 'kirim-tiket:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);

            return back()
                ->withInput()
                ->withErrors(['limit' => "Mohon tunggu {$seconds} detik lagi sebelum mengirim laporan baru."]);
        }

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_hp' => 'required|string|max:20',
            'lokasi' => 'required|string|max:255',
            'topik_bantuan' => 'required|string|max:255',
            'deskripsi_umum_masalah' => 'required|string|max:255',
            'penjelasan_lengkap' => 'required|string',
            'gambar.*' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        RateLimiter::hit($key, 60);

        $validated = $this->sanitizeTicketPayload($validated);
        $validated['gambar'] = $this->storeUploadedImages($request, 'gambar', 'laporan-gambar');

        $ticket = Ticket::create($validated);

        $this->sendEmailNotification($ticket);
        $this->sendWhatsAppNotification($ticket);

        return redirect()->route('laporan.sukses', ['uuid' => $ticket->uuid]);
    }

    private function sendEmailNotification(Ticket $ticket): void
    {
        $linkTracking = TicketSecurity::trackingUrl($ticket);
        $subject = "[IT Helpdesk] Laporan Anda Telah Diterima - #{$ticket->no_tiket}";

        $htmlBody = "
        <html>
            <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <div style='max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; padding: 20px;'>
                    <h2 style='color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px;'>
                        Laporan IT Helpdesk PTPN IV
                    </h2>
                    <p>Halo <strong>" . e($ticket->nama_lengkap) . "</strong>,</p>
                    <p>Terima kasih telah melaporkan kendala IT kepada kami. Laporan Anda telah berhasil diterima dan sedang kami proses.</p>
                    <div style='background-color: #ecf0f1; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                        <p style='margin: 5px 0;'><strong>Nomor Tiket:</strong> " . e($ticket->no_tiket) . "</p>
                        <p style='margin: 5px 0;'><strong>Kategori:</strong> " . e($ticket->topik_bantuan) . "</p>
                        <p style='margin: 5px 0;'><strong>Status:</strong> Dalam Antrian</p>
                        <p style='margin: 5px 0;'><strong>Tanggal:</strong> " . e($ticket->created_at->format('d M Y H:i')) . "</p>
                    </div>
                    <p style='margin-top: 20px; margin-bottom: 10px;'><strong>Cara Melacak Laporan Anda:</strong></p>
                    <p>Kunjungi link berikut untuk melihat status terbaru laporan Anda:</p>
                    <p><a href='" . e($linkTracking) . "' style='background-color: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Lacak Laporan Anda</a></p>
                    <p style='margin-top: 20px; color: #7f8c8d; font-size: 12px;'>
                        Atau copy-paste link ini di browser: <br>
                        " . e($linkTracking) . "
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

    private function sendWhatsAppNotification(Ticket $ticket): void
    {
        \App\Jobs\SendWhatsAppNotification::dispatch($ticket);
    }

    public function success(string $uuid): View
    {
        $ticket = $this->findTicketByUuid($uuid);

        return view('sukses', compact('ticket'));
    }

    public function cek(Request $request): View|RedirectResponse
    {
        $uuid = (string) $request->query('uuid', '');

        if ($uuid === '') {
            return redirect()->route('home');
        }

        $ticket = $this->findTicketByUuid($uuid);
        $hasAccess = $this->hasTicketAccess($request, $ticket);

        return view('lacak', [
            'ticket' => $ticket,
            'isExpired' => $hasAccess ? $this->isTicketExpired($ticket) : false,
            'adminSudahJawab' => $hasAccess ? $ticket->comments()->whereNotNull('user_id')->exists() : false,
            'requiresVerification' => ! $hasAccess,
            'accessToken' => $hasAccess ? TicketSecurity::generateAccessToken($ticket) : null,
        ]);
    }

    public function authorizeAccess(Request $request, string $uuid): RedirectResponse
    {
        $ticket = $this->findTicketByUuid($uuid);

        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        if (strcasecmp(trim($validated['email']), (string) $ticket->email) !== 0) {
            return redirect()
                ->route('laporan.cek', ['uuid' => $ticket->uuid])
                ->withErrors(['email' => 'Email tidak cocok dengan tiket ini.']);
        }

        return redirect()->route('laporan.cek', [
            'uuid' => $ticket->uuid,
            'token' => TicketSecurity::generateAccessToken($ticket),
        ]);
    }

    public function reply(Request $request, string $uuid): JsonResponse|RedirectResponse
    {
        $ticket = $this->findTicketByUuid($uuid);

        if (! $this->hasTicketAccess($request, $ticket)) {
            return $this->forbiddenResponse($request, 'Akses tiket tidak valid atau sudah kedaluwarsa.');
        }

        $request->validate([
            'isi_pesan' => 'required|string',
            'token' => 'required|string',
            'attachments.*' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        if ($this->isTicketExpired($ticket)) {
            return $this->validationResponse($request, 'Tiket ini sudah ditutup permanen dan tidak bisa dibalas lagi.');
        }

        $adminSudahJawab = $ticket->comments()->whereNotNull('user_id')->exists();

        if (! $adminSudahJawab) {
            return $this->validationResponse($request, 'Mohon tunggu balasan dari Admin terlebih dahulu sebelum mengirim pesan.');
        }

        $attachmentPaths = $this->storeUploadedImages($request, 'attachments', 'comment-attachments');
        $content = TicketSecurity::sanitizeRichText($request->input('isi_pesan'));

        $ticket->comments()->create([
            'user_id' => null,
            'content' => $content,
            'attachments' => $attachmentPaths,
        ]);

        if ($ticket->status !== 'Open') {
            $ticket->update(['status' => 'Open', 'reopened_at' => now(), 'solved_at' => null]);
        } else {
            $ticket->update(['reopened_at' => now()]);
        }

        if ($request->expectsJson()) {
            $html = view('partials.chat_single', [
                'comment' => $ticket->comments()->latest()->first(),
                'ticket' => $ticket,
            ])->render();

            return response()->json([
                'success' => true,
                'message' => 'Pesan terkirim!',
                'html' => $html,
            ]);
        }

        return back()->with('success', 'Pesan terkirim!');
    }

    public function chatHistory(Request $request): JsonResponse
    {
        $uuid = (string) $request->query('uuid', '');

        if ($uuid === '') {
            return response()->json(['error' => 'UUID required'], 400);
        }

        $ticket = $this->findTicketByUuid($uuid);

        if (! $this->hasTicketAccess($request, $ticket)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $html = view('partials.chat_history', compact('ticket'))->render();
        $adminSudahJawab = $ticket->comments()->whereNotNull('user_id')->exists();

        return response()->json([
            'html' => $html,
            'status' => $ticket->status,
            'adminSudahJawab' => $adminSudahJawab,
            'isExpired' => $this->isTicketExpired($ticket),
        ]);
    }

    private function isTicketExpired(Ticket $ticket): bool
    {
        $days = 5;

        if ($ticket->resolutionSla) {
            $days = (int) $ticket->resolutionSla->response_days;
        } elseif ($ticket->sla) {
            $days = (int) $ticket->sla->response_days;
        }

        return $ticket->created_at->copy()->addDays($days)->isPast() || $ticket->status === 'Closed';
    }

    public function uploadTrixImage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'uuid' => 'required|uuid',
            'token' => 'required|string',
            'file' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        $ticket = $this->findTicketByUuid($validated['uuid']);

        if (! TicketSecurity::hasValidAccessToken($ticket, $validated['token'])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $path = $request->file('file')->store('trix-attachments', 'public');

        return response()->json(['url' => asset('storage/' . $path)]);
    }

    private function sanitizeTicketPayload(array $validated): array
    {
        $validated['nama_lengkap'] = TicketSecurity::sanitizePlainText($validated['nama_lengkap']);
        $validated['email'] = strtolower(trim($validated['email']));
        $validated['no_hp'] = preg_replace('/\D+/', '', $validated['no_hp']) ?: '';
        $validated['lokasi'] = TicketSecurity::sanitizePlainText($validated['lokasi']);
        $validated['topik_bantuan'] = TicketSecurity::sanitizePlainText($validated['topik_bantuan']);
        $validated['deskripsi_umum_masalah'] = TicketSecurity::sanitizePlainText($validated['deskripsi_umum_masalah']);
        $validated['penjelasan_lengkap'] = TicketSecurity::sanitizeRichText($validated['penjelasan_lengkap']);

        return $validated;
    }

    private function storeUploadedImages(Request $request, string $field, string $directory): ?array
    {
        $paths = [];

        if (! $request->hasFile($field)) {
            return null;
        }

        foreach ((array) $request->file($field) as $file) {
            if ($file && $file->isValid()) {
                $paths[] = $file->store($directory, 'public');
            }
        }

        return ! empty($paths) ? array_values($paths) : null;
    }

    private function findTicketByUuid(string $uuid): Ticket
    {
        return Ticket::where('uuid', $uuid)->firstOrFail();
    }

    private function hasTicketAccess(Request $request, Ticket $ticket): bool
    {
        $token = $request->query('token', $request->input('token'));

        return TicketSecurity::hasValidAccessToken($ticket, is_string($token) ? $token : null);
    }

    private function validationResponse(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }

        return back()->withErrors(['status' => $message]);
    }

    private function forbiddenResponse(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 403);
        }

        return redirect()->route('home')->withErrors(['status' => $message]);
    }
}
