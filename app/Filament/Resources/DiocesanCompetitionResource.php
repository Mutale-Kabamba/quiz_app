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

                Select::make('scope_type')
                    ->label('Participation Scope')
                    ->options([
                        'diocese' => 'Diocese Level (All Livingstone Diocese Youth)',
                        'deanery' => 'Deanery Level (Restricted to Selected Deanery)',
                        'parish' => 'Parish Level (Restricted to Selected Parish)',
                        'custom' => 'Custom Invitational (Individual Youth Accounts with Personal Codes)',
                    ])
                    ->default('diocese')
                    ->reactive()
                    ->required(),

                Select::make('competition_type')
                    ->label('Event Classification')
                    ->options([
                        'diocesan' => 'Diocesan Championship',
                        'deanery' => 'Deanery Championship',
                        'parish' => 'Parish Tournament',
                        'youth_rally' => 'Youth Congress / Rally',
                    ])
                    ->default('diocesan')
                    ->required(),

                TextInput::make('rally_pin')
                    ->label('Multiplayer PIN / Entry Code')
                    ->default(fn () => 'LV-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5)))
                    ->maxLength(15)
                    ->helperText('Shared code for open scope rallies. Custom rallies generate personal codes.')
                    ->required(),

                Select::make('deanery_id')
                    ->relationship('deanery', 'name')
                    ->label('Target Deanery')
                    ->visible(fn ($get) => $get('scope_type') === 'deanery')
                    ->required(fn ($get) => $get('scope_type') === 'deanery'),

                Select::make('parish_id')
                    ->relationship('parish', 'name')
                    ->label('Target Parish')
                    ->visible(fn ($get) => $get('scope_type') === 'parish')
                    ->required(fn ($get) => $get('scope_type') === 'parish'),

                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Formation Category / Track (Optional)'),

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
                        'closed' => 'Closed',
                    ])
                    ->default('scheduled')
                    ->required(),

                DateTimePicker::make('start_time')
                    ->label('Scheduled Start Time'),

                DateTimePicker::make('end_time')
                    ->label('Scheduled End Time'),

                DateTimePicker::make('registration_open_at')
                    ->label('Registration Opens At'),

                DateTimePicker::make('registration_close_at')
                    ->label('Registration Closes At'),

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

                TextColumn::make('scope_type')
                    ->label('Scope')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'diocese' => 'info',
                        'deanery' => 'primary',
                        'parish' => 'warning',
                        'custom' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->sortable(),

                TextColumn::make('participants_count')
                    ->counts('participants')
                    ->label('Participants')
                    ->badge()
                    ->color('success'),

                TextColumn::make('rally_pin')
                    ->label('Entry PIN')
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
                SelectFilter::make('scope_type')
                    ->options([
                        'diocese' => 'Diocese Level',
                        'deanery' => 'Deanery Level',
                        'parish' => 'Parish Level',
                        'custom' => 'Custom Invitational',
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

                Action::make('add_participant')
                    ->label('Add Youth')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->visible(fn (DiocesanCompetition $record) => $record->isCustomScope())
                    ->form([
                        Select::make('user_id')
                            ->label('Select Youth Member')
                            ->options(function () {
                                return \App\Models\User::where('role', 'youth')
                                    ->with('parish')
                                    ->get()
                                    ->mapWithKeys(function ($user) {
                                        $parishName = $user->parish?->name ?? 'No Parish';
                                        return [$user->id => "{$user->name} ({$parishName}) - {$user->email}"];
                                    });
                            })
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (DiocesanCompetition $record, array $data) {
                        $user = \App\Models\User::findOrFail($data['user_id']);
                        $service = app(\App\Services\RallyAccessService::class);
                        $participant = $service->addCustomParticipant($record, $user, Auth::user());

                        Notification::make()
                            ->title('Participant Added')
                            ->body("{$user->name} added with personal code: {$participant->access_code}")
                            ->success()
                            ->send();
                    }),

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
