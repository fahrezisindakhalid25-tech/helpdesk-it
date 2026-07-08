<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\Setting;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Section;

class PengaturanAI extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Pengaturan AI NLP';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $title = 'Pengaturan AI NLP';
    protected static string $view = 'filament.pages.pengaturan-a-i';

    public ?array $data = [];

    public function mount(): void
    {
        $setting = Setting::where('key', 'ai_confidence_threshold')->first();
        $this->form->fill([
            'ai_confidence_threshold' => $setting ? ($setting->value * 100) : 20,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Konfigurasi Model mDeBERTa')
                    ->description('Atur ambang batas (threshold) probabilitas AI. Jika skor keyakinan AI di bawah angka ini, maka tiket akan otomatis masuk ke kategori "Lain-lain".')
                    ->schema([
                        TextInput::make('ai_confidence_threshold')
                            ->label('Confidence Threshold (%)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->suffix('%')
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $value = $data['ai_confidence_threshold'] / 100;
        
        Setting::updateOrCreate(
            ['key' => 'ai_confidence_threshold'],
            ['value' => $value]
        );

        Notification::make()
            ->success()
            ->title('Berhasil disimpan')
            ->body('Pengaturan Threshold AI telah diperbarui.')
            ->send();
    }
}
