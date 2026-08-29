<?php

namespace App\Filament\Resources;

use App\Filament\Resources\YouthUserResource\Pages;
use App\Models\Parish;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\XpLedgerService;
use BackedEnum;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use UnitEnum;

class YouthUserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';
    protected static string | UnitEnum | null $navigationGroup = 'Youth Ministry';
    protected static ?string $navigationLabel = 'Diocesan Youth Directory';
    protected static ?string $modelLabel = 'Youth Member';

    public static function canViewAny(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'youth');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Youth Full Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Phone Number')
                    ->tel()
                    ->required()
                    ->unique(User::class, 'phone', ignoreRecord: true),

                TextInput::make('email')
                    ->label('Email (Optional)')
                    ->email()
                    ->unique(User::class, 'email', ignoreRecord: true),

                Select::make('parish_id')
                    ->relationship('parish', 'name')
                    ->label('Assigned Parish')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('status')
                    ->options([
                        'approved' => 'Active / Approved',
                        'rejected' => 'Suspended',
                        'pending' => 'Pending Verification',
                    ])
                    ->default('approved')
                    ->required(),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->default('password123')
                    ->required(fn ($context) => $context === 'create')
                    ->visibleOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('parish.name')
                    ->label('Parish')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('parish.deanery.name')
                    ->label('Deanery')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('level')
                    ->label('Level')
                    ->badge()
                    ->formatStateUsing(fn ($state) => "Lvl {$state}")
                    ->sortable(),

                TextColumn::make('xp')
                    ->label('Total XP')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' XP')
                    ->sortable(),

                TextColumn::make('current_streak')
                    ->label('Streak')
                    ->formatStateUsing(fn ($state) => "🔥 {$state}d")
                    ->sortable(),

                TextColumn::make('last_activity_date')
                    ->label('Last Active')
                    ->date('M d, Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->filters([
                SelectFilter::make('parish_id')
                    ->relationship('parish', 'name')
                    ->label('Filter by Parish'),

                SelectFilter::make('status')
                    ->options([
                        'approved' => 'Active',
                        'rejected' => 'Suspended',
                        'pending' => 'Pending',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Action::make('transfer_parish')
                    ->label('Transfer Parish')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->form([
                        Select::make('new_parish_id')
                            ->label('New Parish')
                            ->options(Parish::pluck('name', 'id'))
                            ->required(),
                        Textarea::make('reason')
                            ->label('Reason for Transfer')
                            ->required(),
                    ])
                    ->action(function (User $record, array $data) {
                        $oldParishId = $record->parish_id;
                        $record->update(['parish_id' => $data['new_parish_id']]);

                        app(AuditLogService::class)->log(
                            'youth_parish_transferred',
                            $record,
                            ['parish_id' => $oldParishId],
                            ['parish_id' => $data['new_parish_id'], 'reason' => $data['reason']]
                        );

                        Notification::make()
                            ->title('Youth Member Transferred')
                            ->body("{$record->name} moved to new parish successfully.")
                            ->success()
                            ->send();
                    }),

                Action::make('correct_xp')
                    ->label('Score Correction')
                    ->icon('heroicon-o-sparkles')
                    ->color('warning')
                    ->form([
                        TextInput::make('adjustment')
                            ->numeric()
                            ->label('XP Adjustment Amount (+ or -)')
                            ->required(),
                        Textarea::make('reason')
                            ->label('Auditable Reason for Score Correction')
                            ->required(),
                    ])
                    ->action(function (User $record, array $data) {
                        app(XpLedgerService::class)->correctXp(
                            Auth::user(),
                            $record,
                            (int) $data['adjustment'],
                            $data['reason']
                        );

                        Notification::make()
                            ->title('Score Adjusted via Ledger')
                            ->body("XP ledger transaction and audit log updated.")
                            ->success()
                            ->send();
                    }),

                Action::make('toggle_status')
                    ->label(fn (User $record) => $record->status === 'approved' ? 'Suspend' : 'Reactivate')
                    ->icon(fn (User $record) => $record->status === 'approved' ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle')
                    ->color(fn (User $record) => $record->status === 'approved' ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function (User $record) {
                        $newStatus = $record->status === 'approved' ? 'rejected' : 'approved';
                        $record->update(['status' => $newStatus]);

                        app(AuditLogService::class)->log(
                            $newStatus === 'approved' ? 'youth_reactivated' : 'youth_suspended',
                            $record,
                            ['status' => $record->status],
                            ['status' => $newStatus]
                        );

                        Notification::make()
                            ->title($newStatus === 'approved' ? 'Youth Reactivated' : 'Youth Suspended')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListYouthUsers::route('/'),
            'create' => Pages\CreateYouthUser::route('/create'),
            'edit' => Pages\EditYouthUser::route('/{record}/edit'),
        ];
    }
}
