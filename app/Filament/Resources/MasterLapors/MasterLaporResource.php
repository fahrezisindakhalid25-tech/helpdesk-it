<?php

namespace App\Filament\Resources\MasterLapors;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\MasterLapors\Pages\ListMasterLapors;
use App\Filament\Resources\MasterLapors\Pages\CreateMasterLapor;
use App\Filament\Resources\MasterLapors\Pages\EditMasterLapor;
use App\Filament\Resources\MasterLaporResource\Pages;
use App\Filament\Resources\MasterLaporResource\RelationManagers;
use App\Models\MasterLapor;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MasterLaporResource extends Resource
{
    protected static ?string $model = MasterLapor::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';
    
    protected static string | \UnitEnum | null $navigationGroup = 'Master Data';
    
    protected static ?string $navigationLabel = 'Data Karyawan';

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
                TextInput::make('nik')
                    ->label('NIK')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('nama')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),
                TextInput::make('no_hp')
                    ->label('No HP / WhatsApp')
                    ->tel()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('no_hp')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMasterLapors::route('/'),
            'create' => CreateMasterLapor::route('/create'),
            'edit' => EditMasterLapor::route('/{record}/edit'),
        ];
    }
}
