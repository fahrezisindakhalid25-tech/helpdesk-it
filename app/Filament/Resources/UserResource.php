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
                Forms\Components\Section::make('User Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Access Control (Permissions)')
                    ->description('Select directly what this user can do.')
                    ->schema([
                        Forms\Components\CheckboxList::make('permissions')
                            ->options([
                                '*' => 'SUPER ADMIN (Full Access)', // Wildcard
                                
                                'ticket.view' => 'View Tickets',
                                'ticket.create' => 'Create Tickets',
                                'ticket.update' => 'Update Tickets (Reply/Status)',
                                'ticket.delete' => 'Delete Tickets',
                                
                                // Dashboard
                                'dashboard.view' => 'View Dashboard Stats',
                                
                                'category.view' => 'View Categories',
                                'category.manage' => 'Manage Categories',
                                
                                'location.view' => 'View Locations',
                                'location.manage' => 'Manage Locations',
                                
                                'sla.view' => 'View SLAs',
                                'sla.manage' => 'Manage SLAs',
                                
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
                    ->color(fn ($state) => in_array('*', is_array($state) ? $state : []) ? 'success' : 'info')
                    ->formatStateUsing(function ($state) {
                        $state = is_array($state) ? $state : [];
                        if (in_array('*', $state)) return 'Super Admin';
                        return count($state) . ' Permissions';
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
