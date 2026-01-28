<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Pengguna')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Hak Akses (Permissions)')
                    ->description('Pilih apa yang boleh dilakukan pengguna ini.')
                    ->schema([
                        Forms\Components\CheckboxList::make('permissions')
                            ->label('Daftar Izin')
                            ->options([
                                '*' => 'SUPER ADMIN (Akses Penuh)', // Wildcard
                                
                                'ticket.view' => 'View Tickets',
                                'ticket.create' => 'Create Tickets',
                                'ticket.update' => 'Update Tickets (Reply/Status)',
                                'ticket.delete' => 'Delete Tickets',
                                'ticket.change_sla' => 'Change Ticket SLA',
                                'ticket.export' => 'Export Tickets',
                                
                                // Dashboard
                                'dashboard.view' => 'View Dashboard Stats',
                                
                                'category.view' => 'View Categories',
                                'category.manage' => 'Manage Categories',
                                
                                'location.view' => 'View Locations',
                                'location.manage' => 'Manage Locations',
                                
                                'sla.view' => 'View SLAs',
                                'sla.manage' => 'Manage SLAs',
                                
                                'master_lapor.view' => 'View Data Karyawan',
                                'master_lapor.manage' => 'Manage Data Karyawan',
                                
                                'user.view' => 'View Users',
                                'user.manage' => 'Manage Users',
                            ])
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('permissions')
                    ->badge()
                    ->color(fn ($state) => $state === '*' ? 'success' : 'primary')
                    ->formatStateUsing(function ($state) {
                        $labels = [
                            '*' => 'SUPER ADMIN',
                            'ticket.view' => 'Lihat Tiket',
                            'ticket.create' => 'Buat Tiket',
                            'ticket.update' => 'Update Tiket',
                            'ticket.delete' => 'Hapus Tiket',
                            'ticket.change_sla' => 'Ubah SLA Tiket',
                            'ticket.export' => 'Export Tiket',
                            'dashboard.view' => 'Lihat Dashboard',
                            'category.view' => 'Lihat Kategori',
                            'category.manage' => 'Kelola Kategori',
                            'location.view' => 'Lihat Lokasi',
                            'location.manage' => 'Kelola Lokasi',
                            'sla.view' => 'Lihat SLA',
                            'sla.manage' => 'Kelola SLA',
                            'master_lapor.view' => 'Lihat Data Karyawan',
                            'master_lapor.manage' => 'Kelola Data Karyawan',
                            'user.view' => 'Lihat Pengguna',
                            'user.manage' => 'Kelola Pengguna',
                        ];
                        return $labels[$state] ?? $state;
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
