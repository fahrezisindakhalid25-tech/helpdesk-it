<?php

namespace App\Filament\Resources\Pelapor;

use App\Filament\Resources\Pelapor\Pages\CreatePelapor;
use App\Filament\Resources\Pelapor\Pages\EditPelapor;
use App\Filament\Resources\Pelapor\Pages\ListPelapor;
use App\Models\Master\Pelapor;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PelaporResource extends Resource
{
    protected static ?string $model = Pelapor::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Master';

    protected static ?string $navigationLabel = 'Pelapor';

    // === AUTHORIZATION ===
    public static function canViewAny(): bool
    {
        return auth()->user()->hasPermission('master_lapor.view') || auth()->user()->hasPermission('master_lapor.manage');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasPermission('master_lapor.manage');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasPermission('master_lapor.manage');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasPermission('master_lapor.manage');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->heading('Pelapor')
                    ->description('Informasi personal pelapor keluhan layanan TI')
                    ->components([
                        TextInput::make('NIK')
                            ->label('NIK')
                            ->required()
                            ->length(16)
                            ->unique(ignoreRecord: true),
                        TextInput::make('nama')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(80),
                        TextInput::make('email')
                            ->label('Email')
                            ->required()
                            ->email()
                            ->maxLength(80),
                        TextInput::make('no_hp')
                            ->label('Handphone')
                            ->tel()
                            ->maxLength(14),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('NIK')
                    ->label('NIK')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('no_hp')
                    ->label('Handphone')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Tanggal ditambahkan')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Terakhir diperbarui')
                    ->since()
                    ->dateTimeTooltip()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPelapor::route('/'),
            'create' => CreatePelapor::route('/create'),
            'edit' => EditPelapor::route('/{record}/edit'),
        ];
    }
}
