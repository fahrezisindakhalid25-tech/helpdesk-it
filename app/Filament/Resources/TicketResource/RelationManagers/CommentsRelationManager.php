<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';
    protected static ?string $title = 'Aktivitas & Diskusi';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('content')
                    ->label('Tulis Balasan...')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content')
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\TextColumn::make('user.name')
                            ->weight('bold')
                            ->icon('heroicon-m-user-circle')
                            ->color('primary'),

                        Tables\Columns\TextColumn::make('created_at')
                            ->dateTime('d M Y - H:i')
                            ->color('gray')
                            ->alignEnd(),
                    ]),

                    Tables\Columns\TextColumn::make('content')
                        ->wrap()
                        ->extraAttributes(['class' => 'py-2']),
                ])->space(3),
            ])
            ->contentGrid([
                'md' => 1,
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Reply / Balas Pesan')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->modalHeading('Tulis Balasan Anda')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        return $data;
                    }),
            ])
            ->actions([])
            ->defaultSort('created_at', 'asc');
    }
}