<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParishResource\Pages;
use App\Models\Parish;
use App\Services\AuditLogService;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ParishResource extends Resource
{
    protected static ?string $model = Parish::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';
    protected static string | UnitEnum | null $navigationGroup = 'Diocesan Governance';
    protected static ?string $navigationLabel = 'Parishes Directory';

    public static function canViewAny(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Parish Name')
                    ->placeholder("e.g. St. Theresa's Cathedral")
                    ->required()
                    ->maxLength(255),

                TextInput::make('code')
                    ->label('Parish Code')
                    ->placeholder('e.g. ST-THERESA')
                    ->maxLength(50),

                Select::make('deanery_id')
                    ->relationship('deanery', 'name')
                    ->label('Assigned Deanery')
                    ->required(),

                TextInput::make('location')
                    ->label('Location / Town')
                    ->placeholder('e.g. Livingstone Central')
                    ->maxLength(255),

                TextInput::make('contact_email')
                    ->label('Parish Office Email')
                    ->email(),

                TextInput::make('contact_phone')
                    ->label('Parish Office Phone')
                    ->tel(),

                Toggle::make('is_active')
                    ->label('Parish Active in Platform')
                    ->default(true),

                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
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

                TextColumn::make('deanery.name')
                    ->label('Deanery')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('users_count')
                    ->label('Youth Members')
                    ->counts('users')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('location')
                    ->searchable(),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('deanery_id')
                    ->relationship('deanery', 'name')
                    ->label('Filter by Deanery'),

                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Action::make('toggle_status')
                    ->label(fn (Parish $record) => $record->is_active ? 'Deactivate' : 'Reactivate')
                    ->icon(fn (Parish $record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Parish $record) => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function (Parish $record) {
                        $newStatus = !$record->is_active;
                        $record->update(['is_active' => $newStatus]);

                        app(AuditLogService::class)->log(
                            $newStatus ? 'parish_reactivated' : 'parish_deactivated',
                            $record,
                            ['is_active' => !$newStatus],
                            ['is_active' => $newStatus]
                        );

                        Notification::make()
                            ->title($newStatus ? 'Parish Reactivated' : 'Parish Deactivated')
                            ->success()
                            ->send();
                    }),
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
            'index' => Pages\ListParishes::route('/'),
            'create' => Pages\CreateParish::route('/create'),
            'edit' => Pages\EditParish::route('/{record}/edit'),
        ];
    }
}
