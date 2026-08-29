<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeaneryResource\Pages;
use App\Models\Deanery;
use BackedEnum;
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

class DeaneryResource extends Resource
{
    protected static ?string $model = Deanery::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-building-library';
    protected static string | UnitEnum | null $navigationGroup = 'Diocesan Governance';
    protected static ?string $navigationLabel = 'Deaneries';

    public static function canViewAny(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Deanery Name')
                    ->placeholder('e.g. Livingstone Deanery')
                    ->required()
                    ->maxLength(255),

                TextInput::make('code')
                    ->label('Deanery Code (Unique)')
                    ->placeholder('e.g. LIV')
                    ->unique(Deanery::class, 'code', ignoreRecord: true)
                    ->required()
                    ->maxLength(10),

                Textarea::make('description')
                    ->label('Description / Geographical Coverage')
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

                TextColumn::make('code')
                    ->badge()
                    ->color('primary')
                    ->searchable(),

                TextColumn::make('parishes_count')
                    ->label('Parishes')
                    ->counts('parishes')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime('M d, Y')
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
            'index' => Pages\ListDeaneries::route('/'),
            'create' => Pages\CreateDeanery::route('/create'),
            'edit' => Pages\EditDeanery::route('/{record}/edit'),
        ];
    }
}
