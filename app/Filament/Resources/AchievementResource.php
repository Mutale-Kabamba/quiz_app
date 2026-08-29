<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AchievementResource\Pages;
use App\Models\Achievement;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AchievementResource extends Resource
{
    protected static ?string $model = Achievement::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-trophy';
    protected static string | UnitEnum | null $navigationGroup = 'Gamification & Challenges';
    protected static ?string $navigationLabel = 'Milestone Badges';

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
                TextInput::make('code')
                    ->unique(Achievement::class, 'code', ignoreRecord: true)
                    ->required(),

                TextInput::make('title')
                    ->required(),

                Textarea::make('description')
                    ->rows(2)
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('icon')
                    ->label('Icon (Emoji e.g. 🏆, 🔥, 📜)')
                    ->default('🏆')
                    ->required(),

                Select::make('type')
                    ->options([
                        'streak' => 'Daily Formation Streak (Days)',
                        'lesson_count' => 'Completed Lessons Count',
                        'quiz_count' => 'Completed Quizzes Count',
                        'flashcard_count' => 'Reviewed Flashcards Count',
                        'xp_total' => 'Total XP Reached',
                    ])
                    ->default('streak')
                    ->required(),

                TextInput::make('threshold')
                    ->numeric()
                    ->default(1)
                    ->required(),

                TextInput::make('xp_reward')
                    ->numeric()
                    ->default(100)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('icon')
                    ->label('Badge'),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->badge(),

                TextColumn::make('threshold')
                    ->sortable(),

                TextColumn::make('xp_reward')
                    ->label('Bonus XP')
                    ->formatStateUsing(fn ($state) => "+{$state} XP"),
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
            'index' => Pages\ListAchievements::route('/'),
            'create' => Pages\CreateAchievement::route('/create'),
            'edit' => Pages\EditAchievement::route('/{record}/edit'),
        ];
    }
}
