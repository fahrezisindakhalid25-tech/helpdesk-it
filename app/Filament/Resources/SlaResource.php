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
                        ->label('Label')
                        ->placeholder('Contoh: first response, resolution, 2 jam'),

                    // 2. Number & Durasi
                    Forms\Components\Section::make('Pengaturan Waktu')
                        ->schema([
                            Forms\Components\TextInput::make('response_days')
                                ->label('Number')
                                ->placeholder('Contoh: 2 Hari, Urgent, dsb'),
                                
                            Forms\Components\TextInput::make('response_time')
                                ->type('time')
                                ->step(1)
                                ->label('Durasi')
                                ->placeholder('HH:MM:SS'),
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
                    
                Tables\Columns\TextColumn::make('response_days')
                    ->label('Number')
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