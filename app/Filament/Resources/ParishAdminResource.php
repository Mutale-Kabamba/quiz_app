<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParishAdminResource\Pages;
use App\Models\Parish;
use App\Models\User;
use App\Services\AuditLogService;
use BackedEnum;
use Filament\Forms\Components\Select;
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

class ParishAdminResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-user-group';
    protected static string | UnitEnum | null $navigationGroup = 'Diocesan Governance';
    protected static ?string $navigationLabel = 'Parish Administrators';
    protected static ?string $modelLabel = 'Parish Administrator';

    public static function canViewAny(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('role', ['chairperson', 'deanery_admin', 'parish_admin']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Administrator Full Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Phone Number (e.g. +260970000000)')
                    ->tel()
                    ->required()
                    ->unique(User::class, 'phone', ignoreRecord: true),

                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->unique(User::class, 'email', ignoreRecord: true),

                Select::make('parish_id')
                    ->relationship('parish', 'name')
                    ->label('Assigned Parish')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('role')
                    ->options([
                        'chairperson' => 'Parish Youth Chairperson / Admin',
                        'deanery_admin' => 'Deanery Youth Coordinator',
                    ])
                    ->default('chairperson')
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

                TextColumn::make('email')
                    ->searchable(),

                TextColumn::make('phone')
                    ->searchable(),

                TextColumn::make('parish.name')
                    ->label('Parish')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('role')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => $state === 'deanery_admin' ? 'Deanery Admin' : 'Parish Admin'),

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

                Action::make('reset_password')
                    ->label('Reset Password')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->form([
                        TextInput::make('new_password')
                            ->label('New Temporary Password')
                            ->password()
                            ->default('password123')
                            ->required(),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->update(['password' => Hash::make($data['new_password'])]);

                        app(AuditLogService::class)->log(
                            'admin_password_reset',
                            $record,
                            null,
                            ['user_id' => $record->id]
                        );

                        Notification::make()
                            ->title('Password Reset Successful')
                            ->body("Password for {$record->name} updated.")
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
                            $newStatus === 'approved' ? 'admin_reactivated' : 'admin_suspended',
                            $record,
                            ['status' => $record->status],
                            ['status' => $newStatus]
                        );

                        Notification::make()
                            ->title($newStatus === 'approved' ? 'Administrator Reactivated' : 'Administrator Suspended')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParishAdmins::route('/'),
            'create' => Pages\CreateParishAdmin::route('/create'),
            'edit' => Pages\EditParishAdmin::route('/{record}/edit'),
        ];
    }
}
