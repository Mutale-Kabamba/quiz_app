<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Models\Question;
use BackedEnum;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static string | UnitEnum | null $navigationGroup = 'Quiz & Content Management';
    protected static ?string $navigationLabel = 'Questions Repository';

    public static function canCreate(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),

                Select::make('level')
                    ->options([
                        1 => 'Level 1: Junior / Foundation (10-15s)',
                        2 => 'Level 2: Intermediate / Youth (15-20s)',
                        3 => 'Level 3: Advanced / Competitor (20-30s)',
                    ])
                    ->default(1)
                    ->required(),

                Textarea::make('question_text')
                    ->label('Question')
                    ->rows(3)
                    ->required()
                    ->columnSpanFull(),

                KeyValue::make('options')
                    ->label('Answer Options (e.g. A, B, C, D)')
                    ->keyLabel('Option Key (A/B/C/D)')
                    ->valueLabel('Option Text')
                    ->default([
                        'A' => '',
                        'B' => '',
                        'C' => '',
                        'D' => '',
                    ])
                    ->required()
                    ->columnSpanFull(),

                Select::make('correct_option_key')
                    ->label('Correct Option Key')
                    ->options([
                        'A' => 'Option A',
                        'B' => 'Option B',
                        'C' => 'Option C',
                        'D' => 'Option D',
                    ])
                    ->required(),

                TextInput::make('reference_citation')
                    ->label('Reference Citation')
                    ->placeholder('e.g., YOUCAT #142, CCC #1213, Matthew 5:3'),

                Textarea::make('explanation')
                    ->label('Theological / Catechetical Explanation')
                    ->rows(3)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Active in Quizzes')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->badge()
                    ->sortable(),

                TextColumn::make('level')
                    ->label('Tier')
                    ->formatStateUsing(fn ($state) => "Level {$state}")
                    ->sortable(),

                TextColumn::make('question_text')
                    ->limit(60)
                    ->searchable(),

                TextColumn::make('correct_option_key')
                    ->label('Key')
                    ->badge()
                    ->color('success'),

                TextColumn::make('reference_citation')
                    ->label('Citation')
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category'),

                SelectFilter::make('level')
                    ->options([
                        1 => 'Level 1: Junior',
                        2 => 'Level 2: Youth',
                        3 => 'Level 3: Advanced',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
