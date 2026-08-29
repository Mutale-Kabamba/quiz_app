<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaintProfileResource\Pages;
use App\Models\SaintProfile;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SaintProfileResource extends Resource
{
    protected static ?string $model = SaintProfile::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-sparkles';

    protected static string | \UnitEnum | null $navigationGroup = 'Knowledge Base & Curriculum';

    protected static ?string $navigationLabel = 'Saints Knowledge Bank';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Saint Profile Details')
                    ->components([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('title_designation')
                            ->placeholder('e.g. Virgin and Martyr, Doctor of the Church')
                            ->maxLength(255),
                        TextInput::make('feast_day_month_day')
                            ->label('Feast Day (MM-DD)')
                            ->placeholder('02-08')
                            ->required()
                            ->maxLength(10),
                        TextInput::make('country_region')
                            ->label('Country / Region')
                            ->placeholder('Sudan, Uganda, Zambia, Italy')
                            ->maxLength(255),
                        Toggle::make('is_african_heritage')
                            ->label('African Heritage Witness')
                            ->default(false),
                        TagsInput::make('patronages')
                            ->placeholder('Add patronage...')
                            ->columnSpanFull(),
                        Textarea::make('biography')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('patronage_prayer')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('title_designation')
                    ->limit(30),
                TextColumn::make('feast_day_month_day')
                    ->label('Feast Day')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('country_region')
                    ->label('Region'),
                IconColumn::make('is_african_heritage')
                    ->label('African Saint')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_african_heritage'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSaintProfiles::route('/'),
            'create' => Pages\CreateSaintProfile::route('/create'),
            'edit' => Pages\EditSaintProfile::route('/{record}/edit'),
        ];
    }
}
