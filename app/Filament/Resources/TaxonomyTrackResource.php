<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaxonomyTrackResource\Pages;
use App\Models\TaxonomyDomain;
use App\Models\TaxonomyTrack;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
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

class TaxonomyTrackResource extends Resource
{
    protected static ?string $model = TaxonomyTrack::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string | \UnitEnum | null $navigationGroup = 'Knowledge Base & Curriculum';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Track Configuration')
                    ->components([
                        Select::make('domain_id')
                            ->label('Taxonomy Domain')
                            ->relationship('domain', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('name')
                            ->label('Track Name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('code')
                            ->label('Track Code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        TextInput::make('icon')
                            ->label('Icon Emoji / Symbol')
                            ->default('📜')
                            ->maxLength(10),
                        ColorPicker::make('color_theme')
                            ->label('Color Accent'),
                        TextInput::make('display_order')
                            ->numeric()
                            ->default(1),
                        Toggle::make('is_active')
                            ->label('Active in Curriculum')
                            ->default(true),
                        Textarea::make('description')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_order')
                    ->sortable()
                    ->label('#'),
                TextColumn::make('icon')
                    ->label('Icon'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('code')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('categories_count')
                    ->counts('categories')
                    ->label('Categories'),
                TextColumn::make('questions_count')
                    ->counts('questions')
                    ->label('Questions'),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTaxonomyTracks::route('/'),
            'create' => Pages\CreateTaxonomyTrack::route('/create'),
            'edit' => Pages\EditTaxonomyTrack::route('/{record}/edit'),
        ];
    }
}
