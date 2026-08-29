<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlashcardResource\Pages;
use App\Models\Flashcard;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class FlashcardResource extends Resource
{
    protected static ?string $model = Flashcard::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-sparkles';
    protected static string | UnitEnum | null $navigationGroup = 'Catechetical Formation';
    protected static ?string $navigationLabel = 'Flashcards Spaced Arena';

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

                Select::make('lesson_id')
                    ->relationship('lesson', 'title')
                    ->label('Associated Lesson (Optional)'),

                Textarea::make('front_text')
                    ->label('Front: Inquiry / Question Prompt')
                    ->rows(3)
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('back_text')
                    ->label('Back: Concise Doctrinal Answer / Core Truth')
                    ->rows(3)
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('reference_citation')
                    ->label('Scripture / CCC / YOUCAT Citation')
                    ->placeholder('e.g. CCC #1213, Matthew 28:19'),

                Select::make('difficulty')
                    ->options([
                        1 => 'Foundation (Level 1)',
                        2 => 'Intermediate (Level 2)',
                        3 => 'Advanced (Level 3)',
                    ])
                    ->default(1)
                    ->required(),

                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ])
                    ->default('published')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->badge()
                    ->sortable(),

                TextColumn::make('front_text')
                    ->label('Prompt')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('reference_citation')
                    ->label('Citation')
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->relationship('category', 'name'),
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
            'index' => Pages\ListFlashcards::route('/'),
            'create' => Pages\CreateFlashcard::route('/create'),
            'edit' => Pages\EditFlashcard::route('/{record}/edit'),
        ];
    }
}
