<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Pengguna')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Hak Akses (Permissions)')
                    ->description('Pilih apa yang boleh dilakukan pengguna ini.')
                    ->schema([
                        CheckboxList::make('permissions')
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
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('permissions')
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
                TextColumn::make('created_at')
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
