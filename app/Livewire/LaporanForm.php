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
use App\Models\MasterLapor;

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
                    ->description('Masukkan NIK Anda untuk melengkapi data otomatis.')
                    ->schema([
                        Forms\Components\TextInput::make('nik')
                            ->label('NIK')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (!$state) return;
                                $karyawan = MasterLapor::where('nik', $state)->first();
                                if ($karyawan) {
                                    $set('nama_lengkap', $karyawan->nama);
                                    $set('email', $karyawan->email);
                                    $set('no_hp', $karyawan->no_hp);
                                }
                            })
                            ->exists('master_lapors', 'nik')
                            ->validationMessages([
                                'exists' => 'NIK tidak terdaftar dalam database kami. Silakan hubungi Admin.',
                            ]),

                        Forms\Components\TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('no_hp')
                            ->label('No WhatsApp')
                            ->tel()
                            ->numeric()
                            ->placeholder('08...')
                            ->nullable(),
                            
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->nullable()
                            ->maxLength(255),
                            
                        Forms\Components\Select::make('lokasi')
                            ->label('Lokasi / Unit Kerja')
                            ->options(Location::query()->pluck('name', 'name'))
                            ->searchable()
                            ->required(),
                    ])->columns(['default' => 1, 'sm' => 2]),

                Forms\Components\Section::make('Detail Masalah')
                    ->schema([
                        Forms\Components\TextInput::make('deskripsi_umum_masalah')
                            ->label('Ringkasan Masalah (Contoh: WiFi Putus)')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('penjelasan_lengkap')
                            ->label('Ceritakan Detail Kendalanya')
                            ->required()
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'bulletList', 'orderedList', 'undo', 'redo'
                            ])
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('gambar')
                            ->label('Foto Langsung / Upload Bukti (Opsional)')
                            ->multiple()
                            ->image()
                            ->directory('laporan-gambar')
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function create()
    {
        $key = 'kirim-tiket:' . request()->ip();
        // if (RateLimiter::tooManyAttempts($key, 1)) {
        //     $seconds = RateLimiter::availableIn($key);
        //     $this->addError('rate_limit', "Mohon tunggu $seconds detik lagi sebelum mengirim laporan baru.");
        //     return;
        // }
        // RateLimiter::hit($key, 60);

        $data = $this->form->getState();

        // Prediksi kategori otomatis via NLP
        try {
            $teksKeluhan = strip_tags($data['deskripsi_umum_masalah'] . '. ' . $data['penjelasan_lengkap']);
            $daftarKategori = Category::pluck('name')->toArray();
            
            $setting = \App\Models\Setting::where('key', 'ai_confidence_threshold')->first();
            $threshold = $setting ? (float)$setting->value : 0.20;
            
            $response = Http::timeout(30)->post('http://127.0.0.1:8000/predict', [
                'description' => $teksKeluhan,
                'categories' => $daftarKategori,
                'threshold' => $threshold
            ]);
            
            if ($response->successful()) {
                $data['topik_bantuan'] = $response->json('predicted_category');
            } else {
                $data['topik_bantuan'] = 'Lain-lain';
            }
        } catch (\Exception $e) {
            \Log::error("NLP API Connection Error: " . $e->getMessage());
            $data['topik_bantuan'] = 'Lain-lain';
        }

        if (isset($data['gambar']) && is_array($data['gambar'])) {
            $data['gambar'] = json_encode(array_values($data['gambar']));
        }

        $ticket = Ticket::create($data);

        \App\Jobs\SendWhatsAppNotification::dispatch($ticket);

        return redirect()->route('laporan.sukses', ['uuid' => $ticket->uuid]);
    }

    public function render()
    {
        return view('livewire.laporan-form');
    }
}