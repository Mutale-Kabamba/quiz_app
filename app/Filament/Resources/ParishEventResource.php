<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParishEventResource\Pages;
use App\Models\ParishEvent;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ParishEventResource extends Resource
{
    protected static ?string $model = ParishEvent::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';
    protected static string | UnitEnum | null $navigationGroup = 'Parish Activities';
    protected static ?string $navigationLabel = 'Parish Events';

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
                    ->label('Event Title')
                    ->required(),

                Select::make('event_type')
                    ->label('Event Type')
                    ->options([
                        'youth_meeting' => 'Youth Meeting',
                        'bible_study' => 'Bible Study',
                        'catechesis' => 'Catechetical Formation Session',
                        'quiz_practice' => 'Parish Quiz Practice',
                        'retreat' => 'Youth Retreat & Recollection',
                        'rally_prep' => 'Deanery Rally Preparation',
                        'competition' => 'Parish Competition',
                    ])
                    ->default('youth_meeting')
                    ->required(),

                DatePicker::make('event_date')
                    ->required(),

                TextInput::make('start_time')
                    ->placeholder('e.g. 14:00')
                    ->label('Start Time'),

                TextInput::make('end_time')
                    ->placeholder('e.g. 16:30')
                    ->label('End Time'),

                TextInput::make('location')
                    ->placeholder('e.g. St. Theresa Parish Hall')
                    ->label('Location'),

                TextInput::make('organizer')
                    ->placeholder('e.g. Parish Youth Executive')
                    ->label('Organizer'),

                Select::make('status')
                    ->options([
                        'published' => 'Published',
                        'draft' => 'Draft',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('published')
                    ->required(),

                Toggle::make('requires_registration')
                    ->label('Requires Advance Registration'),

                TextInput::make('capacity')
                    ->numeric()
                    ->label('Capacity Limit (Optional)'),

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

                TextColumn::make('event_type')
                    ->badge()
                    ->sortable(),

                TextColumn::make('event_date')
                    ->date('M d, Y')
                    ->sortable(),

                TextColumn::make('location')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'completed' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'published' => 'Published',
                        'draft' => 'Draft',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
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
            'index' => Pages\ListParishEvents::route('/'),
            'create' => Pages\CreateParishEvent::route('/create'),
            'edit' => Pages\EditParishEvent::route('/{record}/edit'),
        ];
    }
}
