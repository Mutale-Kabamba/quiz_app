<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParishAnnouncementResource\Pages;
use App\Models\ParishAnnouncement;
use BackedEnum;
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

class ParishAnnouncementResource extends Resource
{
    protected static ?string $model = ParishAnnouncement::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-megaphone';
    protected static string | UnitEnum | null $navigationGroup = 'Communication';
    protected static ?string $navigationLabel = 'Parish Announcements';

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
                    ->label('Announcement Title')
                    ->placeholder('e.g. Tomorrow Youth Gathering at 14:00')
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('message')
                    ->label('Message Content')
                    ->rows(4)
                    ->required()
                    ->columnSpanFull(),

                Select::make('target_type')
                    ->label('Recipients')
                    ->options([
                        'all' => 'All Parish Youth Members',
                        'selected' => 'Active Youth Only',
                    ])
                    ->default('all')
                    ->required(),

                Select::make('priority')
                    ->options([
                        'normal' => 'Standard Announcement',
                        'urgent' => 'High Priority / Urgent',
                        'celebration' => 'Celebration / Congratulations',
                    ])
                    ->default('normal')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'celebration' => 'warning',
                        default => 'primary',
                    }),

                TextColumn::make('target_type')
                    ->formatStateUsing(fn ($state) => $state === 'all' ? 'All Youth' : 'Selected'),

                TextColumn::make('created_at')
                    ->label('Dispatched')
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
            'index' => Pages\ListParishAnnouncements::route('/'),
            'create' => Pages\CreateParishAnnouncement::route('/create'),
            'edit' => Pages\EditParishAnnouncement::route('/{record}/edit'),
        ];
    }
}
