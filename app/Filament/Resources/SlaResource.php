<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SlaResource\Pages;
use App\Models\Sla;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SlaResource extends Resource
{
    protected static ?string $model = Sla::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Master Waktu SLA';
    
public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Card::make()->schema([
                
                // 1. Label (Nama SLA)
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nama SLA')
                    ->placeholder('Contoh: Resolusi Cepat'),

                // 2. KOLOM BARU: NUMBER (Deskripsi Singkat)
                Forms\Components\TextInput::make('number')
                    ->label('Number / Kode') // Label bebas
                    ->placeholder('Contoh: L1, Urgent, VIP')
                    ->maxLength(255), // Tipe Varchar standar

                // 3. Section Durasi (Hari & Jam)
                Forms\Components\Section::make('Durasi Pengerjaan')
                    ->schema([
                        
                        // Kolom Hari
                        Forms\Components\TextInput::make('response_days')
                            ->label('Hari')
                            ->numeric()
                            ->default(0)
                            ->suffix('Hari'),

                        // Kolom Jam : Menit
                        Forms\Components\TimePicker::make('response_time')
                            ->label('Jam : Menit')
                            ->seconds(false)
                            ->format('H:i')
                            ->displayFormat('H:i')
                            ->default('00:00')
                            ->prefix('Pukul'),

                    ])->columns(2),

            ])
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Label')
                    ->searchable(),

                Tables\Columns\TextColumn::make('number')
                    ->label('Kode')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('response_days')
                    ->label('Hari')
                    ->sortable(),

                Tables\Columns\TextColumn::make('response_time')
                    ->label('Durasi'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSlas::route('/'),
            'create' => Pages\CreateSla::route('/create'),
            'edit' => Pages\EditSla::route('/{record}/edit'),
        ];
    }
}