<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Location;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class LaporanForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pelapor')
                    ->description('Data diri Anda untuk keperluan konfirmasi.')
                    ->schema([
                        Forms\Components\TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('no_hp')
                            ->label('No WhatsApp')
                            ->tel()
                            ->numeric()
                            ->placeholder('08...')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('lokasi')
                            ->label('Lokasi / Unit Kerja')
                            ->options(Location::query()->pluck('name', 'name'))
                            ->searchable()
                            ->required(),
                    ])->columns(['default' => 1, 'sm' => 2]),

                Forms\Components\Section::make('Detail Masalah')
                    ->schema([
                        Forms\Components\Select::make('topik_bantuan')
                            ->label('Kategori Masalah')
                            ->options(Category::query()->pluck('name', 'name'))
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('deskripsi_umum_masalah')
                            ->label('Judul Laporan')
                            ->placeholder('Contoh: Printer Macet di Ruang Keuangan')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('penjelasan_lengkap')
                            ->label('Detail Kronologi')
                            ->required()
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'bulletList', 'orderedList', 'undo', 'redo'
                            ])
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('gambar')
                            ->label('Upload Bukti Gambar (Opsional)')
                            ->multiple()
                            ->image()
                            ->directory('laporan-gambar')
                            ->maxSize(5120) // 5MB
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function create()
    {
        // Rate Limiter
        $key = 'kirim-tiket:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('rate_limit', "Mohon tunggu $seconds detik lagi sebelum mengirim laporan baru.");
            return;
        }
        RateLimiter::hit($key, 60);

        $data = $this->form->getState();

        // Handle file upload array to JSON
        if (isset($data['gambar']) && is_array($data['gambar'])) {
            $data['gambar'] = json_encode(array_values($data['gambar']));
        }

        $ticket = Ticket::create($data);

        // Send Notifications
        $this->sendWhatsAppNotification($ticket);

        return redirect()->route('laporan.sukses', ['uuid' => $ticket->uuid]);
    }

    private function sendWhatsAppNotification($ticket) {
        // Logika notifikasi WA (sama seperti sebelumnya)
        // ...
    }

    public function render()
    {
        return view('livewire.laporan-form');
    }
}