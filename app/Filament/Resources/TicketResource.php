<?php

namespace App\Filament\Resources;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $slug = 'laporan-ticket';
    protected static ?string $navigationLabel = 'Laporan Ticket';
    protected static ?string $modelLabel = 'Laporan';

    public static function form(Form $form): Form
    {
        $halamanSaatIni = $form->getOperation(); 

        // === MODE 1: CREATE (Saat Admin Input Manual) ===
        if ($halamanSaatIni === 'create') {
            return $form->schema([
                Forms\Components\Group::make()->columnSpanFull()->schema([
                    Forms\Components\Section::make('Buat Tiket Laporan Baru')
                        ->schema([
                            Forms\Components\TextInput::make('nama_lengkap')->required()->label('Nama Pelapor'),
                            Forms\Components\TextInput::make('email')->email()->required(),
                            Forms\Components\TextInput::make('no_hp')->numeric()->required()->label('No WhatsApp'),
                            
                            Forms\Components\Select::make('lokasi')
                                ->options(\App\Models\Location::pluck('name', 'name'))
                                ->required()->searchable(),
                            
                            Forms\Components\Select::make('topik_bantuan')
                                ->label('Kategori Masalah')
                                ->options(\App\Models\Category::pluck('name', 'name'))
                                ->required()->searchable(),

                            Forms\Components\Textarea::make('deskripsi_umum_masalah')->required()->columnSpanFull()->label('Subjek'),
                            Forms\Components\RichEditor::make('penjelasan_lengkap')->columnSpanFull(),
                        ])->columns(2),
                ]),
            ]);
        }

        // === MODE 2: DETAIL (DASHBOARD ADMIN) ===
        return $form
            ->columns(3)
            ->schema([
                // === KOLOM KIRI (CHAT & DETAIL) ===
                Forms\Components\Group::make()->columnSpan(2)->schema([
                    Forms\Components\Tabs::make('Aktivitas Tiket')->tabs([
                        Forms\Components\Tabs\Tab::make('Activity')->icon('heroicon-m-chat-bubble-left-right')->schema([
                            Forms\Components\Section::make()->schema([
                                Forms\Components\TextInput::make('topik_bantuan')->disabled()->extraAttributes(['class' => 'bg-gray-100']),
                                Forms\Components\Textarea::make('deskripsi_umum_masalah')->rows(2)->disabled()->extraAttributes(['class' => 'font-bold text-lg mb-2']),
                                Forms\Components\RichEditor::make('penjelasan_lengkap')->disabled()->toolbarButtons([]),
                                
                                // TAMPILKAN GAMBAR JIKA ADA
                                Forms\Components\Placeholder::make('gambar_display')
                                    ->hiddenLabel()
                                    ->visible(fn ($record) => $record && $record->gambar)
                                    ->content(fn ($record) => $record && $record->gambar ? new \Illuminate\Support\HtmlString(
                                        '<div class="mt-3"><p class="text-sm font-semibold text-gray-700 mb-2">Lampiran Gambar:</p><div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">' . 
                                        collect(json_decode($record->gambar, true) ?? [])->map(fn($img) => '<img src="' . asset('storage/' . $img) . '" alt="Gambar Laporan" onclick="openAdminModal(this.src)" style="max-width: 300px; border-radius: 8px; border: 1px solid #ddd; box-shadow: 0 1px 3px rgba(0,0,0,0.1); cursor: zoom-in;" class="zoom-img">')
                                        ->join('') . 
                                        '</div><style>.zoom-img{transition: box-shadow 0.2s;}.zoom-img:hover{box-shadow: 0 4px 12px rgba(0,0,0,0.15);}</style></div><div id="admin-modal" style="display:none;position:fixed;z-index:1000;left:0;top:0;width:100%;height:100%;background-color:rgba(0,0,0,0.7);" onclick="closeAdminModal()"><div style="position:relative;margin:auto;top:50%;transform:translateY(-50%);"><img id="admin-modal-image" style="max-width:90vw;max-height:90vh;margin:auto;display:block;cursor:pointer;" onclick="event.stopPropagation()"><div style="position:absolute;top:10px;right:10px;color:white;font-size:28px;font-weight:bold;cursor:pointer;" onclick="closeAdminModal()">×</div><div style="position:absolute;bottom:10px;left:50%;transform:translateX(-50%);display:flex;gap:10px;"><button onclick="adminZoomIn()" style="padding:8px 12px;background:white;border:none;border-radius:4px;cursor:pointer;">+ 20%</button><button onclick="adminResetZoom()" style="padding:8px 12px;background:white;border:none;border-radius:4px;cursor:pointer;">Reset</button><button onclick="adminZoomOut()" style="padding:8px 12px;background:white;border:none;border-radius:4px;cursor:pointer;">- 20%</button></div></div></div><script>let adminZoomLevel=100;function openAdminModal(src){document.getElementById("admin-modal").style.display="block";document.getElementById("admin-modal-image").src=src;adminZoomLevel=100;updateAdminImageZoom()}function closeAdminModal(){document.getElementById("admin-modal").style.display="none"}function adminZoomIn(){adminZoomLevel+=20;updateAdminImageZoom()}function adminZoomOut(){if(adminZoomLevel>50){adminZoomLevel-=20}updateAdminImageZoom()}function adminResetZoom(){adminZoomLevel=100;updateAdminImageZoom()}function updateAdminImageZoom(){document.getElementById("admin-modal-image").style.transform="scale("+adminZoomLevel/100+")"}document.addEventListener("keydown",function(e){if(e.key==="Escape"){closeAdminModal()}})</script>'
                                    ) : '-'),
                            ])->compact(),
                            
                            Forms\Components\Placeholder::make('separator')->content(new \Illuminate\Support\HtmlString('<div class="border-t border-gray-200 my-4"></div>')),

                            // CHAT HISTORY
                            Forms\Components\Placeholder::make('chat_history')->label('Percakapan')->content(fn ($record) => $record ? new \Illuminate\Support\HtmlString(
                                collect($record->comments)->map(fn($c) => '
                                <div class="flex gap-3 mb-4">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-600">'.substr($c->user->name ?? 'U', 0, 1).'</div>
                                    <div class="flex-1 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="font-bold text-sm text-gray-800">'.($c->user->name ?? 'User').'</span>
                                            <span class="text-xs text-gray-500">'.$c->created_at->diffForHumans().'</span>
                                        </div>
                                        <p class="text-sm text-gray-700 whitespace-pre-wrap">'.$c->content.'</p>
                                    </div>
                                </div>')->join('')
                            ) : '-'),

                            Forms\Components\Textarea::make('new_comment_content')->label('Balas Pesan')->rows(3)->dehydrated(false),

                            // TOMBOL KIRIM
                            Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make('kirim_balasan')
                                    ->label('Post Reply')->color('primary')->icon('heroicon-m-paper-airplane')
                                    ->visible(fn ($record) => !$record->isClosed())
                                    ->action(function ($record, $get, $set) {
                                        if (!$get('new_comment_content')) return;
                                        $content = $get('new_comment_content');
                                        $record->comments()->create(['user_id' => auth()->id(), 'content' => $content]);
                                        
                                        $dataUpdate = ['last_reply_at' => now()];
                                        if (empty($record->replied_at)) $dataUpdate['replied_at'] = now();
                                        
                                        if (!in_array($record->status, ['Solved', 'Closed'])) {
                                            $dataUpdate['status'] = 'Replied';
                                            $dataUpdate['solved_at'] = null; // Reset solved jika dibalas
                                            $set('status', 'Replied'); 
                                        }
                                        $record->update($dataUpdate);
                                        $set('new_comment_content', '');
                                        
                                        // === KIRIM NOTIFIKASI KE USER ===
                                        self::sendAdminReplyNotification($record, $content);
                                        
                                        \Filament\Notifications\Notification::make()->title('Terkirim')->success()->send();
                                    }),
                            ])->alignRight(),
                        ]),
                        Forms\Components\Tabs\Tab::make('Data Pelapor')
                                ->icon('heroicon-m-user')
                                ->schema([
                                    Forms\Components\TextInput::make('nik')->label('NIK')->disabled(),
                                    Forms\Components\TextInput::make('nama_lengkap')->label('Nama Lengkap')->disabled(),
                                    Forms\Components\TextInput::make('email')->label('Email')->disabled(),
                                    Forms\Components\TextInput::make('no_hp')->label('No WhatsApp')->disabled(),
                                    Forms\Components\TextInput::make('lokasi')->label('Lokasi')->disabled(),
                                ])->columns(2),
                    ]),
                ]),

                // === KOLOM KANAN (SIDEBAR: SEKARANG ADA 2 DROPDOWN SLA) ===
                Forms\Components\Group::make()->columnSpan(1)->schema([
                    Forms\Components\Section::make('Kontrol SLA')->schema([
                        
                        Forms\Components\Placeholder::make('header_custom')->content(fn ($record) => new \Illuminate\Support\HtmlString('
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-lg">'.substr($record->nama_lengkap ?? 'A', 0, 1).'</div>
                                <div><div class="font-bold text-gray-900">'.($record->nama_lengkap ?? '-').'</div><div class="text-xs text-gray-500">Pelapor</div></div>
                            </div>')),

                        Forms\Components\Placeholder::make('separator_sla')->content(new \Illuminate\Support\HtmlString('<div class="border-t border-gray-200 my-2"></div>')),

                        // ==========================================
                        // 1. DROPDOWN KHUSUS FIRST RESPONSE
                        // ==========================================
                        Forms\Components\Select::make('sla_id')
                            ->label('SLA: First Response')
                            ->relationship('sla', 'name')
                            ->placeholder('Pilih SLA Respon')
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($record, $state) {
                                // Hanya update SLA ID, tanpa mengubah status
                                $record->update(['sla_id' => $state]);
                            }),

                        // TIMER FIRST RESPONSE
                        Forms\Components\Placeholder::make('first_response_timer')
                            ->hiddenLabel()
                            ->content(function ($record, $get) { 
                                if ($record->replied_at) {
                                    $text = $record->created_at->diff($record->replied_at)->format('%ad %hh %im %ss');
                                    return new \Illuminate\Support\HtmlString("<div class='bg-blue-50 p-2 rounded border border-blue-200 text-center'><span class='text-blue-700 font-bold'>✓ Responded</span><div class='text-xs'>$text</div></div>");
                                }
                                // GUNAKAN $record->sla_id LANGSUNG DARI DATABASE
                                $slaId = $record->sla_id ?? $get('sla_id');
                                if (!$slaId) return new \Illuminate\Support\HtmlString('<div class="text-xs text-gray-400 text-center">- Pilih SLA Respon -</div>');

                                // Ambil Data SLA (Hari + Jam)
                                $sla = \App\Models\Sla::find($slaId);
                                if (!$sla) return '-';
                                
                                $days = (int) $sla->response_days; 
                                $timeParts = explode(':', $sla->response_time ?? '00:00:00');
                                $deadline = $record->created_at->copy()->addDays($days)->addHours((int)$timeParts[0])->addMinutes((int)$timeParts[1])->timestamp * 1000;

                                return new \Illuminate\Support\HtmlString("
                                    <div class='flex justify-between items-center bg-gray-50 p-2 rounded border mb-4'>
                                        <div x-data=\"{ target: $deadline, now: new Date().getTime(), text: '...', update() { this.now = new Date().getTime(); let d = this.target - this.now; if (d < 0) { this.text = 'OVERDUE'; } else { let days = Math.floor(d / 86400000); let hours = Math.floor((d % 86400000) / 3600000); let mins = Math.floor((d % 3600000) / 60000); let secs = Math.floor((d % 60000) / 1000); this.text = days + 'd ' + hours + 'h ' + mins + 'm ' + secs + 's'; } }, init() { this.update(); setInterval(() => this.update(), 1000); } }\" x-init=\"init()\">
                                            <span x-text=\"text\" class='text-sm font-bold text-red-600'></span>
                                        </div>
                                        <div class='text-xs text-gray-500 text-right'>Target<br>{$timeParts[0]}h {$timeParts[1]}m</div>
                                    </div>
                                ");
                            }),

                        Forms\Components\Placeholder::make('separator_2')->content(new \Illuminate\Support\HtmlString('<div class="border-t border-gray-200 my-2"></div>')),

                        // ==========================================
                        // 2. DROPDOWN SLA RESOLUSI (FINAL)
                        // ==========================================
                        Forms\Components\Select::make('resolution_sla_id')
                            ->label('SLA: Resolution (Final)')
                            ->relationship('sla', 'name') // Menggunakan relasi yang sama ke tabel SLAs
                            ->placeholder('Pilih SLA Resolusi')
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($record, $state) {
                                $record->update(['resolution_sla_id' => $state]);
                            }),

                        // TIMER RESOLUTION
                        Forms\Components\Placeholder::make('resolution_timer')
                            ->hiddenLabel()
                            ->content(function ($record, $get) { 
                                if ($record->status === 'Solved' || $record->status === 'Closed') {
                                    $text = $record->created_at->diff($record->solved_at ?? $record->closed_at ?? now())->format('%ad %hh %im %ss');
                                    return new \Illuminate\Support\HtmlString("<div class='bg-green-50 p-2 rounded border border-green-200 text-center'><span class='text-green-700 font-bold'>✓ Solved</span><div class='text-xs'>$text</div></div>");
                                }
                                
                                $slaId = $record->resolution_sla_id ?? $get('resolution_sla_id');
                                if (!$slaId) return new \Illuminate\Support\HtmlString('<div class="text-xs text-gray-400 text-center">- Pilih SLA Resolusi -</div>');

                                $sla = \App\Models\Sla::find($slaId);
                                if (!$sla) return '-';
                                
                                // Gunakan response_days (karena user menggunakan field Durasi Pengerjaan yang ada)
                                $days = (int) $sla->response_days; 
                                
                                // Ambil jam dari response_time
                                $timeParts = explode(':', $sla->response_time ?? '00:00:00');
                                
                                $deadline = $record->created_at->copy()->addDays($days)->addHours((int)$timeParts[0])->addMinutes((int)$timeParts[1])->timestamp * 1000;

                                return new \Illuminate\Support\HtmlString("
                                    <div class='flex justify-between items-center bg-gray-50 p-2 rounded border mb-4'>
                                        <div x-data=\"{ target: $deadline, now: new Date().getTime(), text: '...', update() { this.now = new Date().getTime(); let d = this.target - this.now; if (d < 0) { this.text = 'OVERDUE'; } else { let days = Math.floor(d / 86400000); let hours = Math.floor((d % 86400000) / 3600000); let mins = Math.floor((d % 3600000) / 60000); let secs = Math.floor((d % 60000) / 1000); this.text = days + 'd ' + hours + 'h ' + mins + 'm ' + secs + 's'; } }, init() { this.update(); setInterval(() => this.update(), 1000); } }\" x-init=\"init()\">
                                            <span x-text=\"text\" class='text-sm font-bold text-red-600'></span>
                                        </div>
                                        <div class='text-xs text-gray-500 text-right'>Target<br>{$days} Hari</div>
                                    </div>
                                ");
                            }),

                        Forms\Components\Placeholder::make('separator_3')->content(new \Illuminate\Support\HtmlString('<div class="border-t border-gray-200 my-4"></div>')),

                        // STATUS SELECT
                        Forms\Components\Select::make('status')
                            ->options(['Open' => 'Open', 'Replied' => 'Replied', 'Solved' => 'Solved', 'Closed' => 'Closed'])
                            ->required()->native(false)->live()
                            ->afterStateUpdated(function ($record, $state) {
                                $d = ['status' => $state];
                                if ($state === 'Solved') { $d['solved_at'] = now(); }
                                elseif ($state === 'Open') { $d['solved_at'] = null; $d['closed_at'] = null; $d['reopened_at'] = now(); }
                                elseif ($state === 'Closed') { $d['closed_at'] = now(); }
                                $record->update($d);
                            }),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_tiket')
                    ->searchable()
                    ->badge()
                    ->color(function ($record) {
                        // Cek apakah ada chat baru dari user (user_id IS NULL) setelah reply terakhir admin
                        $lastComment = $record->comments()->latest()->first();
                        if ($lastComment && $lastComment->user_id === null) {
                            return 'danger'; // Merah jika chat terakhir dari user
                        }
                        return null; // Tidak ada badge jika normal
                    })
                    ->formatStateUsing(function ($state, $record) {
                        $lastComment = $record->comments()->latest()->first();
                        if ($lastComment && $lastComment->user_id === null) {
                             // Tambahkan indicator teks atau biarkan badge warna saja
                             // Keterbatasan badge di TextColumn bawaan mungkin hanya styling.
                             // Kita return state asli, tapi warnanya sudah dihandle 'color'
                        }
                        return $state;
                    })
                    ->label('No Tiket'),
                    
                Tables\Columns\TextColumn::make('nama_lengkap')->searchable(),
                Tables\Columns\TextColumn::make('topik_bantuan')->limit(20)->label('Kategori'),
                // Sla Name dihapus sesuai request
                
                // === SLA FIRST RESPONSE TIMER ===
                Tables\Columns\TextColumn::make('sla_timer')
                    ->label('SLA Respon')
                    ->html()
                    ->getStateUsing(function ($record) {
                        if ($record->replied_at) {
                            $durasi = $record->created_at->diff($record->replied_at)->format('%ad %hh %im');
                            return "<div class='text-xs text-green-600 font-bold'>✓ Done<br><span class='font-normal text-gray-500'>$durasi</span></div>";
                        }
                        if (!$record->sla_id || !$record->sla) return '<span class="text-xs text-gray-400">-</span>';
                        
                        $timeParts = explode(':', $record->sla->response_time ?? '00:00:00');
                        $deadline = $record->created_at->copy()->addDays((int)$record->sla->response_days)->addHours((int)$timeParts[0])->addMinutes((int)$timeParts[1])->timestamp * 1000;
                        
                        return "<div x-data=\"{ target: $deadline, now: new Date().getTime(), text: '...', update() { this.now = new Date().getTime(); let d = this.target - this.now; if (d < 0) { this.text = 'OVERDUE'; } else { let days = Math.floor(d / 86400000); let hours = Math.floor((d % 86400000) / 3600000); let mins = Math.floor((d % 3600000) / 60000); let secs = Math.floor((d % 60000) / 1000); this.text = days + 'd ' + hours + 'h ' + mins + 'm ' + secs + 's'; } }, init() { this.update(); setInterval(() => this.update(), 1000); } }\" x-init=\"init()\">
                                <span x-text=\"text\" class='text-xs font-bold' :class=\"text === 'OVERDUE' ? 'text-red-600' : 'text-blue-600'\"></span>
                            </div>";
                    }),

                // === SLA RESOLUTION TIMER (BARU) ===
                Tables\Columns\TextColumn::make('sla_resolution_timer')
                    ->label('SLA Resolution')
                    ->html()
                    ->getStateUsing(function ($record) {
                         // Jika sudah selesai (Solved/Closed)
                        if ($record->status === 'Solved' || $record->status === 'Closed') {
                            $end = $record->solved_at ?? $record->closed_at ?? now();
                            $durasi = $record->created_at->diff($end)->format('%ad %hh %im');
                            return "<div class='text-xs text-green-600 font-bold'>✓ Solved<br><span class='font-normal text-gray-500'>$durasi</span></div>";
                        }
                        
                        // Cek SLA Resolution ID
                        $slaId = $record->resolution_sla_id; 
                        if (!$slaId) return '<span class="text-xs text-gray-400">-</span>';

                        // Ambil Data SLA (Gunakan Relasi resolutionSla di Model Ticket jika ada, atau manual)
                        // Karena di TicketResource belum didefinisikan with('resolutionSla'), kita fetch manual atau pakai relasi kalau ada.
                        // Di Model Ticket tadi ada 'resolutionSla'. Mari kita pakai itu.
                        $sla = $record->resolutionSla; 
                        if (!$sla) return '<span class="text-xs text-gray-400">-</span>';
                        
                        // Hitung Deadline (Sama seperti Sidebar)
                        $days = (int) $sla->response_days; // Pakai response_days karena user pakai field itu
                        $timeParts = explode(':', $sla->response_time ?? '00:00:00');
                        
                        $deadline = $record->created_at->copy()->addDays($days)->addHours((int)$timeParts[0])->addMinutes((int)$timeParts[1])->timestamp * 1000;
                        
                        return "<div x-data=\"{ target: $deadline, now: new Date().getTime(), text: '...', update() { this.now = new Date().getTime(); let d = this.target - this.now; if (d < 0) { this.text = 'OVERDUE'; } else { let days = Math.floor(d / 86400000); let hours = Math.floor((d % 86400000) / 3600000); let mins = Math.floor((d % 3600000) / 60000); let secs = Math.floor((d % 60000) / 1000); this.text = days + 'd ' + hours + 'h ' + mins + 'm ' + secs + 's'; } }, init() { this.update(); setInterval(() => this.update(), 1000); } }\" x-init=\"init()\">
                                <span x-text=\"text\" class='text-xs font-bold' :class=\"text === 'OVERDUE' ? 'text-red-600' : 'text-blue-600'\"></span>
                            </div>";
                    }),

                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Solved' => 'success', 'Replied' => 'info', 'Open' => 'warning', 'Closed' => 'danger', default => 'gray'
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([Tables\Actions\EditAction::make()->label('Detail')->icon('heroicon-m-eye'), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }

    // === FUNGSI KIRIM NOTIFIKASI KE USER (EMAIL & WHATSAPP) ===
    private static function sendAdminReplyNotification($ticket, $replyContent)
    {
        // Kirim Email
        self::sendAdminReplyEmail($ticket, $replyContent);
        
        // Kirim WhatsApp
        self::sendAdminReplyWhatsApp($ticket, $replyContent);
    }

    // === KIRIM EMAIL BALASAN ADMIN ===
    private static function sendAdminReplyEmail($ticket, $replyContent)
    {
        $linkTracking = route('laporan.cek', [], true) . '?uuid=' . $ticket->uuid;
        $subject = "[IT Helpdesk] Balasan Atas Laporan Anda - #{$ticket->no_tiket}";
        
        $htmlBody = "
        <html>
            <head>
                <meta charset='utf-8'>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #2c3e50; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                    .content { background-color: #ecf0f1; padding: 20px; border: 1px solid #bdc3c7; }
                    .reply-box { background-color: white; padding: 15px; margin: 15px 0; border-left: 4px solid #3498db; }
                    .footer { background-color: #34495e; color: white; padding: 15px; text-align: center; border-radius: 0 0 5px 5px; font-size: 12px; }
                    .button { background-color: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 15px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Balasan dari Admin IT Helpdesk</h2>
                    </div>
                    <div class='content'>
                        <p>Halo <strong>{$ticket->nama_lengkap}</strong>,</p>
                        
                        <p>Admin kami telah memberikan balasan untuk laporan Anda:</p>
                        
                        <div class='reply-box'>
                            <p><strong>Tiket #:</strong> {$ticket->no_tiket}</p>
                            <p><strong>Kategori:</strong> {$ticket->topik_bantuan}</p>
                            <hr>
                            <p>" . nl2br(e($replyContent)) . "</p>
                        </div>
                        
                        <p>Silakan cek status laporan dan berikan balasan Anda di link berikut:</p>
                        <p><a href='{$linkTracking}' class='button'>Lihat Detail Laporan</a></p>
                        
                        <p style='margin-top: 20px; color: #7f8c8d; font-size: 12px;'>
                            Atau copy-paste link ini di browser: <br>
                            {$linkTracking}
                        </p>
                    </div>
                    <div class='footer'>
                        <p><strong>IT Helpdesk PTPN IV</strong></p>
                        <p>Jangan balas email ini, gunakan link di atas untuk merespons.</p>
                    </div>
                </div>
            </body>
        </html>
        ";
        
        try {
            \Mail::html($htmlBody, function ($message) use ($ticket, $subject) {
                $message->to($ticket->email)
                    ->subject($subject)
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });
        } catch (\Exception $e) {
            \Log::error('Email balasan admin gagal dikirim untuk Ticket #' . $ticket->no_tiket . ': ' . $e->getMessage());
        }
    }

    // === KIRIM WHATSAPP BALASAN ADMIN ===
    private static function sendAdminReplyWhatsApp($ticket, $replyContent)
    {
        $token = env('WA_API_TOKEN');
        if (!$token) {
            \Log::warning('WA_API_TOKEN tidak ditemukan di ENV');
            return;
        }

        $phone = self::formatPhoneNumber($ticket->no_hp);
        $linkTracking = route('laporan.cek', [], true) . '?uuid=' . $ticket->uuid;

        // Potong reply content agar tidak terlalu panjang di WA (max 1024 char)
        $shortReply = substr($replyContent, 0, 200);
        if (strlen($replyContent) > 200) {
            $shortReply .= '...';
        }

        $message = "BALASAN DARI ADMIN IT HELPDESK PTPN IV\n\n"
            . "Halo {$ticket->nama_lengkap},\n\n"
            . "Tiket #: {$ticket->no_tiket}\n"
            . "Kategori: {$ticket->topik_bantuan}\n\n"
            . "Balasan Admin:\n"
            . $shortReply . "\n\n"
            . "Lacak dan balas di:\n"
            . "{$linkTracking}\n\n"
            . "Terima kasih atas kesabaran Anda.";

        try {
            // Kirim via Fonnte API dengan Authorization header
            \Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            \Log::error('WhatsApp balasan admin gagal dikirim untuk Ticket #' . $ticket->no_tiket . ': ' . $e->getMessage());
        }
    }

    // === HELPER: FORMAT NOMOR TELEPON ===
    private static function formatPhoneNumber($phone)
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

    // === LOAD DATA SLA DARI DATABASE KE FORM ===
    public static function mutateFormDataBeforeFill(array $data): array
    {
        // Pastikan sla_id dan resolution_sla_id dimuat dari database
        return $data;
    }
    
    public static function getRelations(): array { return []; }
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder { return parent::getEloquentQuery()->with(['sla']); }
}