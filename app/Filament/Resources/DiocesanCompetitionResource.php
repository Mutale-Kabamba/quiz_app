<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiocesanCompetitionResource\Pages;
use App\Models\DiocesanCompetition;
use App\Services\AuditLogService;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class DiocesanCompetitionResource extends Resource
{
    protected static ?string $model = DiocesanCompetition::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-trophy';
    protected static string | UnitEnum | null $navigationGroup = 'Competitions & Rallies';
    protected static ?string $navigationLabel = 'Diocesan Competitions & Rallies';

    public static function canViewAny(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Competition / Rally Title')
                    ->placeholder('e.g. 2026 Livingstone Diocesan Youth Bible Rally')
                    ->required(),

                Select::make('competition_type')
                    ->options([
                        'diocesan' => 'Diocesan Championship (All Deaneries & Parishes)',
                        'deanery' => 'Deanery Level Competition',
                        'parish' => 'Parish Rally',
                        'youth_rally' => 'Special Youth Rally / Congress',
                    ])
                    ->default('diocesan')
                    ->required(),

                TextInput::make('rally_pin')
                    ->label('Multiplayer PIN Code')
                    ->default(fn () => (string) rand(100000, 999999))
                    ->maxLength(10)
                    ->required(),

                Select::make('deanery_id')
                    ->relationship('deanery', 'name')
                    ->label('Target Deanery (If Deanery Competition)'),

                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Study Track / Category (Optional)'),

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
                    ->default(20)
                    ->label('Total Questions Count')
                    ->required(),

                Select::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'live' => 'Live Now / Active',
                        'paused' => 'Paused',
                        'completed' => 'Completed',
                        'draft' => 'Draft',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('scheduled')
                    ->required(),

                DateTimePicker::make('start_time')
                    ->label('Scheduled Start'),

                DateTimePicker::make('end_time')
                    ->label('Scheduled End'),

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

                TextColumn::make('competition_type')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('rally_pin')
                    ->label('Rally PIN')
                    ->badge()
                    ->color('warning')
                    ->copyable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'live' => 'danger',
                        'scheduled' => 'success',
                        'completed' => 'gray',
                        default => 'warning',
                    }),

                TextColumn::make('start_time')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('competition_type')
                    ->options([
                        'diocesan' => 'Diocesan Championship',
                        'deanery' => 'Deanery Level',
                        'parish' => 'Parish Rally',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'live' => 'Live Now',
                        'scheduled' => 'Scheduled',
                        'completed' => 'Completed',
                        'draft' => 'Draft',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Action::make('start_live')
                    ->label('Go Live')
                    ->icon('heroicon-o-play')
                    ->color('danger')
                    ->visible(fn (DiocesanCompetition $record) => in_array($record->status, ['scheduled', 'paused']))
                    ->action(function (DiocesanCompetition $record) {
                        $record->update(['status' => 'live']);

                        app(AuditLogService::class)->log(
                            'competition_started_live',
                            $record,
                            null,
                            ['competition_id' => $record->id, 'pin' => $record->rally_pin]
                        );

                        Notification::make()
                            ->title('Competition is now LIVE')
                            ->body("Participants can connect with PIN: {$record->rally_pin}")
                            ->success()
                            ->send();
                    }),

                Action::make('end_competition')
                    ->label('End Rally')
                    ->icon('heroicon-o-stop')
                    ->color('gray')
                    ->visible(fn (DiocesanCompetition $record) => $record->status === 'live')
                    ->action(function (DiocesanCompetition $record) {
                        $record->update(['status' => 'completed']);

                        app(AuditLogService::class)->log(
                            'competition_ended',
                            $record,
                            null,
                            ['competition_id' => $record->id]
                        );

                        Notification::make()
                            ->title('Competition Concluded')
                            ->info()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiocesanCompetitions::route('/'),
            'create' => Pages\CreateDiocesanCompetition::route('/create'),
            'edit' => Pages\EditDiocesanCompetition::route('/{record}/edit'),
        ];
    }
}
