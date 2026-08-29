<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-shield-check';
    protected static string | UnitEnum | null $navigationGroup = 'Governance & Audit';
    protected static ?string $navigationLabel = 'Diocesan Audit Trail';

    public static function canViewAny(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime('M d, Y H:i:s')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Administrator / User')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('action')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('entity_type')
                    ->label('Target Entity')
                    ->formatStateUsing(fn ($state) => class_basename($state ?? '—')),

                TextColumn::make('ip_address')
                    ->label('IP Address'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('action')
                    ->options([
                        'parish_created' => 'Parish Created',
                        'parish_deactivated' => 'Parish Deactivated',
                        'parish_reactivated' => 'Parish Reactivated',
                        'admin_created' => 'Admin Created',
                        'admin_suspended' => 'Admin Suspended',
                        'admin_password_reset' => 'Admin Password Reset',
                        'youth_suspended' => 'Youth Suspended',
                        'youth_reactivated' => 'Youth Reactivated',
                        'youth_parish_transferred' => 'Youth Parish Transferred',
                        'transfer_request_approved' => 'Transfer Request Approved',
                        'xp_score_corrected' => 'XP Score Corrected',
                        'competition_started_live' => 'Competition Started Live',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }
}
