<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParishTransferResource\Pages;
use App\Models\ParishTransfer;
use App\Services\AuditLogService;
use BackedEnum;
use Filament\Forms\Components\Textarea;
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

class ParishTransferResource extends Resource
{
    protected static ?string $model = ParishTransfer::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static string | UnitEnum | null $navigationGroup = 'Youth Ministry';
    protected static ?string $navigationLabel = 'Parish Transfer Requests';

    public static function canViewAny(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Youth Member')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('fromParish.name')
                    ->label('From Parish')
                    ->badge()
                    ->color('danger'),

                TextColumn::make('toParish.name')
                    ->label('Destination Parish')
                    ->badge()
                    ->color('success'),

                TextColumn::make('requester.name')
                    ->label('Requested By')
                    ->sortable(),

                TextColumn::make('reason')
                    ->limit(40)
                    ->tooltip(fn (ParishTransfer $record): string => $record->reason ?? ''),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),

                TextColumn::make('created_at')
                    ->label('Requested Date')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending Approval',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve Transfer')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (ParishTransfer $record) => $record->status === 'pending')
                    ->action(function (ParishTransfer $record) {
                        $youth = $record->user;
                        $oldParishId = $youth->parish_id;

                        // 1. Update Youth Parish Association (Preserves all XP, streaks, quizzes)
                        $youth->update(['parish_id' => $record->to_parish_id]);

                        // 2. Mark transfer as approved
                        $record->update([
                            'status' => 'approved',
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                        ]);

                        // 3. Log Audit Trail
                        app(AuditLogService::class)->log(
                            'transfer_request_approved',
                            $record,
                            ['parish_id' => $oldParishId],
                            ['parish_id' => $record->to_parish_id, 'user_id' => $youth->id]
                        );

                        Notification::make()
                            ->title('Parish Transfer Approved')
                            ->body("{$youth->name} successfully transferred to {$record->toParish->name}.")
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (ParishTransfer $record) => $record->status === 'pending')
                    ->form([
                        Textarea::make('review_notes')
                            ->label('Rejection Reason')
                            ->required(),
                    ])
                    ->action(function (ParishTransfer $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                            'review_notes' => $data['review_notes'],
                        ]);

                        Notification::make()
                            ->title('Transfer Request Rejected')
                            ->danger()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParishTransfers::route('/'),
        ];
    }
}
