<?php

namespace App\Filament\Resources;

use App\Filament\Resources\YouthApprovalResource\Pages;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class YouthApprovalResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-user-group';
    protected static string | UnitEnum | null $navigationGroup = 'Parish Administration';
    protected static ?string $navigationLabel = 'Youth Approvals';
    protected static ?string $modelLabel = 'Youth Member';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('role', 'youth');
        $user = Auth::user();

        // Scope Parish Chairperson strictly to their assigned Parish
        if ($user && $user->role === 'chairperson') {
            $query->where('parish_id', $user->parish_id);
        }

        return $query;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('phone')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('parish.name')
                    ->label('Parish')
                    ->sortable()
                    ->visible(fn () => Auth::user()?->role === 'super_admin'),
                TextColumn::make('parish.deanery.name')
                    ->label('Deanery')
                    ->sortable()
                    ->visible(fn () => Auth::user()?->role === 'super_admin'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('M d, Y')
                    ->sortable(),
                TextColumn::make('approvedBy.name')
                    ->label('Reviewed By')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending Verification',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending'),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (User $record) => $record->status !== 'approved')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Parish Youth')
                    ->modalDescription('Approving this youth unlocks Ranked Mode and qualifies them for Diocesan leaderboard competitions.')
                    ->action(function (User $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => Auth::id(),
                            'approved_at' => now(),
                            'rejection_reason' => null,
                        ]);

                        Notification::make()
                            ->title('Youth Member Approved')
                            ->body("{$record->name} is now approved for ranked competitions.")
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (User $record) => $record->status !== 'rejected')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Reason for Rejection')
                            ->placeholder('e.g., Not verified under this parish.')
                            ->required(),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'approved_by' => Auth::id(),
                            'approved_at' => now(),
                            'rejection_reason' => $data['rejection_reason'],
                        ]);

                        Notification::make()
                            ->title('Youth Member Rejected')
                            ->danger()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkAction::make('bulk_approve')
                    ->label('Approve Selected')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $records->each->update([
                            'status' => 'approved',
                            'approved_by' => Auth::id(),
                            'approved_at' => now(),
                            'rejection_reason' => null,
                        ]);

                        Notification::make()
                            ->title('Selected Youth Approved')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListYouthApprovals::route('/'),
        ];
    }
}
