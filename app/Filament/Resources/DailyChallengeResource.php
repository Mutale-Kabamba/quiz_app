<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyChallengeResource\Pages;
use App\Models\DailyChallenge;
use App\Models\Question;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class DailyChallengeResource extends Resource
{
    protected static ?string $model = DailyChallenge::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-fire';
    protected static string | UnitEnum | null $navigationGroup = 'Gamification & Challenges';
    protected static ?string $navigationLabel = 'Daily Challenges';

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
                DatePicker::make('challenge_date')
                    ->required()
                    ->unique(DailyChallenge::class, 'challenge_date', ignoreRecord: true),

                TextInput::make('title')
                    ->required(),

                Textarea::make('description')
                    ->rows(2)
                    ->columnSpanFull(),

                Select::make('question_ids')
                    ->label('Selected Questions (5 Questions recommended)')
                    ->options(Question::where('is_active', true)->pluck('question_text', 'id'))
                    ->multiple()
                    ->searchable()
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('xp_reward')
                    ->label('Bonus XP Reward')
                    ->numeric()
                    ->default(50)
                    ->required(),

                Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('challenge_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('title')
                    ->searchable(),

                TextColumn::make('xp_reward')
                    ->label('Reward')
                    ->formatStateUsing(fn ($state) => "+{$state} XP"),

                IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => Pages\ListDailyChallenges::route('/'),
            'create' => Pages\CreateDailyChallenge::route('/create'),
            'edit' => Pages\EditDailyChallenge::route('/{record}/edit'),
        ];
    }
}
