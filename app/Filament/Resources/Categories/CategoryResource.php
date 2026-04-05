<?php

namespace App\Filament\Resources\Categories;

use App\Enums\SLAType;
use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Models\Master\Category;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|\UnitEnum|null $navigationGroup = 'Master';

    protected static ?string $navigationLabel = 'Kategori';

    protected static ?string $modelLabel = 'Kategori';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->heading('Kategori')
                ->description('Informasi seputar kategori')
                ->schema([
                    TextInput::make('name')
                        ->label('Nama')
                        ->required()
                        ->maxLength(255),
                ]),
            Section::make()
                ->heading('Service Level Agreements (SLA)')
                ->description('Stopwatch untuk perhitungan SLA')
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
                    ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Kategori')
            ->description('Kategori permasalahan serta kaitannya dengan Service Level Agreements (SLA)')
            ->columns([
                TextColumn::make('name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('response_time')
                    ->label('Response time')
                    ->getStateUsing(fn ($record) => $record->serviceLevelAgreements()->where('type', SLAType::RESPONSE)->first()?->timeunit ?? '-'),
                TextColumn::make('resolution_time')
                    ->label('Resolution time')
                    ->getStateUsing(fn ($record) => $record->serviceLevelAgreements()->where('type', SLAType::RESOLUTION)->first()?->timeunit ?? '-'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
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
