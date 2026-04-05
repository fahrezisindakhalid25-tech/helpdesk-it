<?php

namespace App\Filament\Resources\Categories;

use App\Enums\SLAType;
use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Master\Category;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';
    protected static string | \UnitEnum | null $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Kategori Masalah';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                TextInput::make('name')
                    ->label('Kategori')
                    ->required()
                    ->maxLength(255),
            ]),
            Section::make()
                ->heading('Service Level Agreements (SLA)')
                ->description('Informasikan SLA untuk setiap kategori permasalahan')
                ->schema([
                    Grid::make([
                        'md' => 2,
                    ])->schema([
                        TextInput::make('response_time')
                            ->label('Response time')
                            ->required()
                            ->regex('/^(?=.*\d[dhms])(?:\d+d)?(?:\d+h)?(?:\d+m)?(?:\d+s)?$/'),
                        TextInput::make('resolution_time')
                            ->label('Resolution time'),
                    ])
                ])
                ->columnSpanFull()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Masalah')
                    ->searchable()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
