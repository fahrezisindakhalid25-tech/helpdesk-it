<?php

namespace App\Livewire;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use App\Jobs\SendWhatsAppNotification;
use App\Models\Master\Category;
use App\Models\Master\Location;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;
use App\Models\Master\Pelapor;

class LaporanForm extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pelapor')
                    ->description('Masukkan NIK Anda untuk melengkapi data otomatis.')
                    ->schema([
                        TextInput::make('nik')
                            ->label('NIK')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Set $set) {
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

                        TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            //->readOnly()
                            ->required()
                            ->maxLength(255),

                        TextInput::make('no_hp')
                            ->label('No WhatsApp')
                            ->tel()
                            ->numeric()
                            ->placeholder('08...')
                            ->nullable(), // Tidak wajib

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->nullable() // Tidak wajib
                            ->maxLength(255),

                        Select::make('lokasi')
                            ->label('Lokasi / Unit Kerja')
                            ->options(Location::query()->pluck('name', 'name'))
                            ->searchable()
                            ->required(),
                    ])->columns(['default' => 1, 'sm' => 2]),

                Section::make('Detail Masalah')
                    ->schema([
                        Select::make('topik_bantuan')
                            ->label('Kategori Masalah')
                            ->options(Category::query()->pluck('name', 'name'))
                            ->searchable()
                            ->required(),
                        TextInput::make('deskripsi_umum_masalah')
                            ->label('Judul Laporan')
                            ->placeholder('Contoh: Printer Macet di Ruang Keuangan')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        RichEditor::make('penjelasan_lengkap')
                            ->label('Detail Kronologi')
                            ->required()
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'bulletList', 'orderedList', 'undo', 'redo'
                            ])
                            ->columnSpanFull(),
                        FileUpload::make('gambar')
                            ->label('Foto Langsung / Upload Bukti (Opsional)')
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
        SendWhatsAppNotification::dispatch($ticket);

        return redirect()->route('laporan.sukses', ['uuid' => $ticket->uuid]);
    }

    public function render()
    {
        return view('livewire.laporan-form');
    }
}
