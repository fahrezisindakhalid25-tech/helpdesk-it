<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\SlaResource\Pages\ListSlas;
use App\Filament\Resources\SlaResource\Pages\CreateSla;
use App\Filament\Resources\SlaResource\Pages\EditSla;
use App\Filament\Resources\SlaResource\Pages;
use App\Models\Sla;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SlaResource extends Resource
{
    protected static ?string $model = Sla::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clock';
    protected static string | \UnitEnum | null $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Master Waktu SLA';
    
public static function form(Schema $schema): Schema
{
    return $schema
        ->components([
            Section::make()->schema([
                
                // 1. Label (Nama SLA)
                TextInput::make('name')
                    ->required()
                    ->label('Nama SLA')
                    ->placeholder('Contoh: Resolusi Cepat'),

                // 2. KOLOM BARU: NUMBER (Deskripsi Singkat)
                TextInput::make('number')
                    ->label('Number / Kode') // Label bebas
                    ->placeholder('Contoh: L1, Urgent, VIP')
                    ->maxLength(255), // Tipe Varchar standar

                // 3. Section Durasi (Hari & Jam)
                Section::make('Durasi Pengerjaan')
                    ->schema([
                        
                        // Kolom Hari
                        TextInput::make('response_days')
                            ->label('Hari')
                            ->numeric()
                            ->default(0)
                            ->suffix('Hari'),

                        // Kolom Jam : Menit
                        TimePicker::make('response_time')
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
                TextColumn::make('name')
                    ->label('Label')
                    ->searchable(),

                TextColumn::make('number')
                    ->label('Kode')
                    ->searchable(),
                    
                TextColumn::make('response_days')
                    ->label('Hari')
                    ->sortable(),

                TextColumn::make('response_time')
                    ->label('Durasi'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSlas::route('/'),
            'create' => CreateSla::route('/create'),
            'edit' => EditSla::route('/{record}/edit'),
        ];
    }
}