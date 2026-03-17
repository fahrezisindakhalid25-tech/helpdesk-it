<?php

namespace App\Filament\Resources\TicketResource\RelationManagers; // <--- INI WAJIB BENAR
use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';
    protected static ?string $title = 'Aktivitas & Diskusi';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('content')
                    ->label('Tulis Balasan...')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content')
            // KITA UBAH JADI STACK (TIMELINE VIEW)
            ->columns([
                Stack::make([
                    Split::make([
                        // Nama Pengirim di Kiri
                        TextColumn::make('user.name')
                            ->weight('bold')
                            ->icon('heroicon-m-user-circle')
                            ->color('primary'),

                        // Waktu di Kanan
                        TextColumn::make('created_at')
                            ->dateTime('d M Y - H:i')
                            ->color('gray')
                            ->alignEnd(),
                    ]),

                    // Isi Pesan di Bawahnya
                    TextColumn::make('content')
                        ->wrap() // Agar teks panjang turun ke bawah
                        ->extraAttributes(['class' => 'py-2']), // Kasih jarak dikit
                ])->space(3), // Jarak antar chat
            ])
            ->contentGrid([
                'md' => 1, // Pastikan cuma 1 kolom ke bawah
            ])
            ->headerActions([
                // Tombol Balas Pesan
                CreateAction::make()
                    ->label('Reply / Balas Pesan')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->modalHeading('Tulis Balasan Anda')
                    ->mutateDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        return $data;
                    }),
            ])
            ->recordActions([]) // Hapus tombol edit/delete biar bersih
            ->defaultSort('created_at', 'asc'); // Chat lama di atas, baru di bawah (seperti WA)
    }
}