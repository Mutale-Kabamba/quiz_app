<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudyResourceResource\Pages;
use App\Models\StudyResource;
use App\Models\TaxonomyCategory;
use App\Models\TaxonomyTopic;
use App\Models\TaxonomyTrack;
use App\Services\ContentPipelineService;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StudyResourceResource extends Resource
{
    protected static ?string $model = StudyResource::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-book-open';

    protected static string | \UnitEnum | null $navigationGroup = 'Knowledge Base & Curriculum';

    protected static ?string $navigationLabel = 'Study Resources & Guides';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Curriculum Placement & Type')
                    ->components([
                        Select::make('track_id')
                            ->label('Curriculum Track')
                            ->relationship('track', 'name')
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->required(),
                        Select::make('category_id')
                            ->label('Category')
                            ->options(fn(callable $get) => $get('track_id') ? TaxonomyCategory::where('track_id', $get('track_id'))->pluck('name', 'id') : [])
                            ->searchable()
                            ->reactive(),
                        Select::make('topic_id')
                            ->label('Topic')
                            ->options(fn(callable $get) => $get('category_id') ? TaxonomyTopic::where('category_id', $get('category_id'))->pluck('name', 'id') : [])
                            ->searchable(),
                        Select::make('resource_type')
                            ->options([
                                'STUDY_NOTE' => 'Study Note',
                                'STUDY_GUIDE' => 'Study Guide',
                                'LESSON' => 'Structured Lesson',
                                'SUMMARY' => 'Summary',
                                'DEFINITION' => 'Doctrinal Definition',
                                'BIBLE_STUDY' => 'Bible Study Note',
                                'DEEP_DIVE' => 'Theological Deep Dive',
                                'SAINT_PROFILE' => 'Saint Profile',
                                'PRAYER_GUIDE' => 'Prayer Guide',
                                'REFLECTION' => 'Spiritual Reflection',
                                'REVISION_NOTE' => 'Quick Revision Note',
                            ])
                            ->default('STUDY_NOTE')
                            ->required(),
                        Select::make('level_id')
                            ->label('Formation Level')
                            ->relationship('level', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('estimated_read_minutes')
                            ->label('Estimated Read Time (Mins)')
                            ->numeric()
                            ->default(3)
                            ->required(),
                    ])->columns(3),

                Section::make('Resource Content')
                    ->components([
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('subheading')
                            ->label('Subtitle / Tagline')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('why_this_matters')
                            ->label('Why This Matters (Pastoral Relevance)')
                            ->rows(2)
                            ->columnSpanFull(),
                        Textarea::make('key_idea')
                            ->label('Core Takeaway')
                            ->rows(2)
                            ->columnSpanFull(),
                        RichEditor::make('content_body')
                            ->label('Detailed Study Body')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(40),
                TextColumn::make('track.name')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                TextColumn::make('resource_type')
                    ->badge()
                    ->color('info'),
                TextColumn::make('estimated_read_minutes')
                    ->label('Read Time')
                    ->formatStateUsing(fn($state) => "{$state}m"),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'PUBLISHED' => 'success',
                        'APPROVED' => 'info',
                        'UNDER_REVIEW' => 'warning',
                        'AI_GENERATED', 'IMPORTED' => 'purple',
                        'NEEDS_REVISION', 'REJECTED' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('track_id')
                    ->relationship('track', 'name')
                    ->label('Track'),
                Tables\Filters\SelectFilter::make('resource_type')
                    ->options([
                        'STUDY_NOTE' => 'Study Note',
                        'STUDY_GUIDE' => 'Study Guide',
                        'LESSON' => 'Lesson',
                        'DEEP_DIVE' => 'Deep Dive',
                        'REFLECTION' => 'Reflection',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'PUBLISHED' => 'Published',
                        'APPROVED' => 'Approved',
                        'UNDER_REVIEW' => 'Under Review',
                        'DRAFT' => 'Draft',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve_publish')
                    ->label('Publish')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn($record) => in_array($record->status, ['UNDER_REVIEW', 'AI_GENERATED', 'DRAFT', 'IMPORTED']))
                    ->action(function ($record) {
                        app(ContentPipelineService::class)->approveContent(
                            $record,
                            Auth::user(),
                            true,
                            5,
                            5,
                            'Published via Content Studio'
                        );
                        Notification::make()->title('Study Resource Published')->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudyResources::route('/'),
            'create' => Pages\CreateStudyResource::route('/create'),
            'edit' => Pages\EditStudyResource::route('/{record}/edit'),
        ];
    }
}
