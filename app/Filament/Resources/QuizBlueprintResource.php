<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuizBlueprintResource\Pages;
use App\Models\QuizBlueprint;
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

class QuizBlueprintResource extends Resource
{
    protected static ?string $model = QuizBlueprint::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static string | \UnitEnum | null $navigationGroup = 'Knowledge Base & Curriculum';

    protected static ?string $navigationLabel = 'Quiz Blueprints';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Blueprint Parameters')
                    ->components([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255),
                        Select::make('level_id')
                            ->label('Target Formation Level')
                            ->relationship('level', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('question_count')
                            ->label('Total Questions')
                            ->numeric()
                            ->default(10)
                            ->required(),
                        TextInput::make('time_limit_seconds')
                            ->label('Time Limit (Seconds)')
                            ->numeric()
                            ->default(180)
                            ->required(),
                        TextInput::make('unseen_question_ratio')
                            ->label('Unseen Question %')
                            ->numeric()
                            ->default(70)
                            ->suffix('%')
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Active Blueprint')
                            ->default(true),
                        Textarea::make('description')
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('level.name')
                    ->badge()
                    ->color('info'),
                TextColumn::make('question_count')
                    ->label('Questions'),
                TextColumn::make('time_limit_seconds')
                    ->label('Time Limit')
                    ->formatStateUsing(fn($state) => "{$state}s"),
                TextColumn::make('unseen_question_ratio')
                    ->label('Unseen Ratio')
                    ->formatStateUsing(fn($state) => "{$state}%"),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuizBlueprints::route('/'),
            'create' => Pages\CreateQuizBlueprint::route('/create'),
            'edit' => Pages\EditQuizBlueprint::route('/{record}/edit'),
        ];
    }
}
