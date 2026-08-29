<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParishCompetitionResource\Pages;
use App\Models\ParishCompetition;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ParishCompetitionResource extends Resource
{
    protected static ?string $model = ParishCompetition::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-bolt';
    protected static string | UnitEnum | null $navigationGroup = 'Parish Activities';
    protected static ?string $navigationLabel = 'Parish Competitions & Rallies';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if ($user && $user->role === 'chairperson') {
            $query->where('parish_id', $user->parish_id);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Competition Title')
                    ->placeholder('e.g. St. Theresa Inter-Section Quiz Rally')
                    ->required(),

                TextInput::make('rally_pin')
                    ->label('Live Multiplayer Rally PIN (6 Digits)')
                    ->default(fn () => (string) rand(100000, 999999))
                    ->maxLength(6)
                    ->required(),

                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Target Study Track (Optional)')
                    ->placeholder('All Categories (General Formation)'),

                Select::make('level')
                    ->options([
                        1 => 'Level 1: Junior Foundation',
                        2 => 'Level 2: Youth Intermediate',
                        3 => 'Level 3: Advanced Scholar',
                    ])
                    ->default(1)
                    ->required(),

                TextInput::make('time_limit_seconds')
                    ->numeric()
                    ->default(15)
                    ->label('Seconds per Question')
                    ->required(),

                TextInput::make('question_count')
                    ->numeric()
                    ->default(10)
                    ->label('Number of Questions')
                    ->required(),

                Select::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'active' => 'Active / Live Now',
                        'completed' => 'Completed',
                        'draft' => 'Draft',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('scheduled')
                    ->required(),

                DateTimePicker::make('start_time')
                    ->label('Scheduled Start Time'),

                DateTimePicker::make('end_time')
                    ->label('Scheduled End Time'),

                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('rally_pin')
                    ->label('Rally PIN')
                    ->badge()
                    ->color('warning')
                    ->copyable(),

                TextColumn::make('category.name')
                    ->label('Track')
                    ->placeholder('All Tracks')
                    ->badge(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'danger',
                        'scheduled' => 'success',
                        'completed' => 'gray',
                        default => 'warning',
                    }),

                TextColumn::make('start_time')
                    ->label('Start Date & Time')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
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
            'index' => Pages\ListParishCompetitions::route('/'),
            'create' => Pages\CreateParishCompetition::route('/create'),
            'edit' => Pages\EditParishCompetition::route('/{record}/edit'),
        ];
    }
}
