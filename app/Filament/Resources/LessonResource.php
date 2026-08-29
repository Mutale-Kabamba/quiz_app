<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LessonResource\Pages;
use App\Models\Lesson;
use BackedEnum;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class LessonResource extends Resource
{
    protected static ?string $model = Lesson::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-book-open';
    protected static string | UnitEnum | null $navigationGroup = 'Catechetical Formation';
    protected static ?string $navigationLabel = 'Lessons & Topics';

    public static function canCreate(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),

                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->required()
                    ->unique(Lesson::class, 'slug', ignoreRecord: true),

                TextInput::make('subheading')
                    ->label('Subheading / Summary Tagline')
                    ->columnSpanFull(),

                TextInput::make('estimated_read_minutes')
                    ->label('Estimated Read Time (Mins)')
                    ->numeric()
                    ->default(5)
                    ->required(),

                Select::make('difficulty')
                    ->options([
                        1 => 'Level 1: Junior Foundation',
                        2 => 'Level 2: Youth Intermediate',
                        3 => 'Level 3: Advanced Scholar',
                    ])
                    ->default(1)
                    ->required(),

                TextInput::make('scripture_citations')
                    ->placeholder('e.g. Matthew 28:19, Romans 6:3-4'),

                TextInput::make('catechism_citations')
                    ->placeholder('e.g. CCC 1213-1216, YOUCAT 194'),

                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ])
                    ->default('published')
                    ->required(),

                TextInput::make('display_order')
                    ->numeric()
                    ->default(1),

                Repeater::make('summary_takeaways')
                    ->label('Key Takeaways (Bullet Points)')
                    ->simple(TextInput::make('takeaway')->required())
                    ->columnSpanFull(),

                Repeater::make('content_sections')
                    ->label('Structured Content Sections')
                    ->schema([
                        TextInput::make('heading')->required(),
                        Textarea::make('body')->rows(4)->required(),
                        TextInput::make('scripture_quote')->label('Scripture Quote (Optional)'),
                        TextInput::make('catechism_quote')->label('Catechism Quote (Optional)'),
                    ])
                    ->columnSpanFull(),

                Repeater::make('key_terms')
                    ->label('Key Terms & Definitions')
                    ->schema([
                        TextInput::make('term')->required(),
                        TextInput::make('definition')->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->badge()
                    ->sortable(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('estimated_read_minutes')
                    ->label('Read Time')
                    ->formatStateUsing(fn ($state) => "{$state} min"),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'archived' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('display_order')
                    ->label('Order')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->relationship('category', 'name'),
                SelectFilter::make('status')
                    ->options([
                        'published' => 'Published',
                        'draft' => 'Draft',
                        'archived' => 'Archived',
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
            'index' => Pages\ListLessons::route('/'),
            'create' => Pages\CreateLesson::route('/create'),
            'edit' => Pages\EditLesson::route('/{record}/edit'),
        ];
    }
}
