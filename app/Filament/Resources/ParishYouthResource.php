<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParishYouthResource\Pages;
use App\Models\Parish;
use App\Models\ParishTransfer;
use App\Models\User;
use App\Services\ParishYouthService;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ParishYouthResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static string | UnitEnum | null $navigationGroup = 'Parish Youth Ministry';
    protected static ?string $navigationLabel = 'Parish Youth Directory';
    protected static ?string $modelLabel = 'Parish Youth Member';

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user && in_array($user->role, ['chairperson', 'super_admin', 'deanery_admin']);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('role', 'youth');
        $user = Auth::user();

        // Strict Parish Isolation: Chairperson can ONLY access their own parish
        if ($user && $user->role === 'chairperson') {
            $query->where('parish_id', $user->parish_id);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Phone Number (e.g. +260970000000)')
                    ->tel()
                    ->required()
                    ->unique(User::class, 'phone', ignoreRecord: true),

                TextInput::make('email')
                    ->label('Email Address (Optional)')
                    ->email()
                    ->unique(User::class, 'email', ignoreRecord: true),

                TextInput::make('password')
                    ->label('Account Password')
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

                TextColumn::make('level')
                    ->label('Level')
                    ->badge()
                    ->color('primary')
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
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'approved' => 'Active / Approved',
                        'pending' => 'Pending Verification',
                        'rejected' => 'Suspended',
                    ]),

                Filter::make('inactive_filter')
                    ->label('Inactive (14+ Days)')
                    ->query(fn (Builder $q) => $q->whereNull('last_activity_date')
                        ->orWhere('last_activity_date', '<', now()->subDays(14)->toDateString())
                    ),
            ])
            ->actions([
                ViewAction::make()
                    ->label('Profile')
                    ->icon('heroicon-o-identification'),

                Action::make('send_encouragement')
                    ->label('Encourage')
                    ->icon('heroicon-o-heart')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Send Encouragement')
                    ->modalDescription('Send a standard parish formation motivation notification to this youth.')
                    ->action(function (User $record) {
                        Notification::make()
                            ->title('Encouragement Dispatched')
                            ->body("Formation encouragement sent to {$record->name}.")
                            ->success()
                            ->send();
                    }),

                Action::make('suspend')
                    ->label('Suspend')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn (User $record) => $record->status === 'approved')
                    ->form([
                        Textarea::make('reason')
                            ->label('Suspension Reason')
                            ->required(),
                    ])
                    ->action(function (User $record, array $data) {
                        app(ParishYouthService::class)->suspendYouth(Auth::user(), $record, $data['reason']);
                        Notification::make()
                            ->title('Youth Member Suspended')
                            ->danger()
                            ->send();
                    }),

                Action::make('reactivate')
                    ->label('Reactivate')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (User $record) => $record->status === 'rejected')
                    ->action(function (User $record) {
                        app(ParishYouthService::class)->reactivateYouth(Auth::user(), $record);
                        Notification::make()
                            ->title('Youth Member Reactivated')
                            ->success()
                            ->send();
                    }),

                Action::make('request_transfer')
                    ->label('Transfer')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('gray')
                    ->form([
                        Select::make('to_parish_id')
                            ->label('Destination Parish')
                            ->options(Parish::where('id', '!=', Auth::user()?->parish_id)->pluck('name', 'id'))
                            ->required(),
                        Textarea::make('reason')
                            ->label('Reason for Transfer (e.g. Relocated for school/work)')
                            ->required(),
                    ])
                    ->action(function (User $record, array $data) {
                        app(ParishYouthService::class)->requestTransfer(
                            Auth::user(),
                            $record,
                            (int) $data['to_parish_id'],
                            $data['reason']
                        );

                        Notification::make()
                            ->title('Transfer Request Submitted')
                            ->body("Transfer request for {$record->name} submitted to Super Admin for approval.")
                            ->info()
                            ->send();
                    }),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextColumn::make('name')->weight('bold'),
                TextColumn::make('phone'),
                TextColumn::make('level')->formatStateUsing(fn ($state) => "Level {$state}"),
                TextColumn::make('xp')->formatStateUsing(fn ($state) => number_format($state) . ' XP'),
                TextColumn::make('current_streak')->formatStateUsing(fn ($state) => "{$state} Days"),
                TextColumn::make('status')->badge(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParishYouth::route('/'),
            'create' => Pages\CreateParishYouth::route('/create'),
        ];
    }
}
