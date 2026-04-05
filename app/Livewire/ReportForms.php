<?php

namespace App\Livewire;

use Filament\Actions\ButtonAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Image;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;

class ReportForms extends Component implements HasSchemas
{
    use InteractsWithSchemas, InteractsWithActions;


    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function render()
    {
        return view('livewire.report-forms');
    }

    public function create(): void
    {
        dd($this->form->getState());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Flex::make([
                    TextInput::make('nik')
                        ->label('NIK')
                        ->required(),
                    TextInput::make('nama_lengkap')
                        ->label('Nama Lengkap')
                        ->required(),
                ]),
                Flex::make([
                    TextInput::make('no_hp')
                        ->label('No WhatsApp')
                        ->required(),
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required(),
                ]),
                RichEditor::make('penjelasan_lengkap')
                    ->label('Rincian Kronologi')
                    ->required()
                    ->extraInputAttributes([
                        'class' => 'h-64'
                    ])
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline'],
                        [ToolbarButtonGroup::make('Heading', ['h1', 'h2', 'h3'])->icon('fi-o-heading')],
                        [ToolbarButtonGroup::make('Alignment', ['alignStart', 'alignCenter', 'alignEnd', 'alignJustify'])],
                        ['bulletList', 'orderedList'],
                        // ['table', 'attachFiles'],
                        ['undo', 'redo']
                    ])
                    ->fileAttachmentsDisk('public'),
                FileUpload::make('gambar'),
            ])
            ->statePath('data');
    }
}
