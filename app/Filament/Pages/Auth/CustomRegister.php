<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CustomRegister extends Register
{
    protected const ALLOWED_REGISTRATION_PERMISSIONS = [
        'dashboard.view' => 'Lihat Dashboard',
        'ticket.view' => 'Lihat Tiket',
        'ticket.create' => 'Buat Tiket',
        'ticket.update' => 'Update Tiket',
    ];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                $this->getPermissionsFormComponent(),
                $this->getThemeModeFormComponent(),
            ]);
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label('Name')
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email')
            ->email()
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel());
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Password')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->rule(Password::default())
            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
            ->same('passwordConfirmation');
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label('Password Confirmation')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->dehydrated(false);
    }

    protected function getPermissionsFormComponent(): Component
    {
        return CheckboxList::make('permissions')
            ->label('Permissions')
            ->options(self::ALLOWED_REGISTRATION_PERMISSIONS)
            ->descriptions([
                'dashboard.view' => 'Akses ke halaman dashboard admin.',
                'ticket.view' => 'Bisa melihat daftar tiket.',
                'ticket.create' => 'Bisa membuat tiket dari panel admin.',
                'ticket.update' => 'Bisa membalas dan mengubah tiket.',
            ])
            ->default(['dashboard.view'])
            ->columns(2)
            ->bulkToggleable()
            ->columnSpanFull()
            ->helperText('Hak akses admin lanjutan tidak bisa dipilih dari halaman register.');
    }

    protected function getThemeModeFormComponent(): Component
    {
        return Select::make('theme_mode')
            ->label('Theme Mode')
            ->options([
                'light' => 'Light',
                'dark' => 'Dark',
            ])
            ->default('light')
            ->required();
    }

    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $allowedPermissions = array_keys(self::ALLOWED_REGISTRATION_PERMISSIONS);

        $data['permissions'] = array_values(array_intersect($allowedPermissions, $data['permissions'] ?? []));
        $data['theme_mode'] = in_array($data['theme_mode'] ?? 'light', ['light', 'dark'], true)
            ? $data['theme_mode']
            : 'light';

        return $data;
    }
}
