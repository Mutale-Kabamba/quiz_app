<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionBankResource\Pages;
use App\Models\FormationLevel;
use App\Models\QuestionBankItem;
use App\Models\TaxonomyCategory;
use App\Models\TaxonomyTopic;
use App\Models\TaxonomyTrack;
use App\Services\ContentPipelineService;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class QuestionBankResource extends Resource
{
    protected static ?string $model = QuestionBankItem::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-queue-list';

    protected static string | \UnitEnum | null $navigationGroup = 'Knowledge Base & Curriculum';

    protected static ?string $navigationLabel = 'Universal Question Bank';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Taxonomy & Cognitive Placement')
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
                        Select::make('level_id')
                            ->label('Formation Level')
                            ->relationship('level', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('bloom_id')
                            ->label("Bloom's Cognitive Level")
                            ->relationship('bloomTaxonomy', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('editorial_difficulty')
                            ->options([
                                'VERY_EASY' => 'Very Easy',
                                'EASY' => 'Easy',
                                'MEDIUM' => 'Medium',
                                'HARD' => 'Hard',
                                'VERY_HARD' => 'Very Hard',
                                'EXPERT' => 'Expert',
                            ])
                            ->default('MEDIUM')
                            ->required(),
                    ])->columns(3),

                Section::make('Question Formulation & Teaching Points')
                    ->components([
                        Select::make('question_type')
                            ->options([
                                'MULTIPLE_CHOICE' => 'Multiple Choice (MCQ)',
                                'TRUE_FALSE' => 'True / False',
                                'MULTIPLE_SELECT' => 'Multiple Select',
                                'SHORT_ANSWER' => 'Short Answer',
                                'SCRIPTURE_REFERENCE' => 'Scripture Reference Identification',
                                'TERM_DEFINITION' => 'Term & Definition',
                            ])
                            ->default('MULTIPLE_CHOICE')
                            ->required(),
                        Textarea::make('question_text')
                            ->label('Question Statement')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('reference_citation')
                            ->label('Scripture / Catechism Reference')
                            ->placeholder('e.g. CCC 1213-1216, John 3:5')
                            ->maxLength(255),
                        Textarea::make('explanation')
                            ->label('Authoritative Explanation')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Answer Options')
                    ->components([
                        Repeater::make('options')
                            ->relationship('options')
                            ->schema([
                                TextInput::make('option_key')
                                    ->label('Key')
                                    ->placeholder('A, B, C, D')
                                    ->required()
                                    ->maxLength(10),
                                TextInput::make('option_text')
                                    ->label('Option Text')
                                    ->required()
                                    ->maxLength(500),
                                Toggle::make('is_correct')
                                    ->label('Is Correct Answer')
                                    ->inline(false),
                                TextInput::make('explanation_why_incorrect')
                                    ->label('Why Incorrect (Optional Distractor Feedback)')
                                    ->maxLength(500),
                            ])
                            ->columns(4)
                            ->defaultItems(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('track.name')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('topic.name')
                    ->label('Topic')
                    ->searchable()
                    ->limit(25),
                TextColumn::make('question_text')
                    ->label('Question Statement')
                    ->searchable()
                    ->limit(60)
                    ->tooltip(fn($record) => $record->question_text),
                TextColumn::make('editorial_difficulty')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'VERY_EASY', 'EASY' => 'success',
                        'MEDIUM' => 'info',
                        'HARD' => 'warning',
                        'VERY_HARD', 'EXPERT' => 'danger',
                        default => 'gray',
                    }),
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
                TextColumn::make('health_score')
                    ->label('Health')
                    ->badge()
                    ->color(fn(int $state): string => $state >= 80 ? 'success' : ($state >= 50 ? 'warning' : 'danger'))
                    ->formatStateUsing(fn($state) => "{$state}%"),
                TextColumn::make('times_answered')
                    ->label('Attempts')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('track_id')
                    ->relationship('track', 'name')
                    ->label('Track'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'PUBLISHED' => 'Published',
                        'APPROVED' => 'Approved',
                        'UNDER_REVIEW' => 'Under Review',
                        'AI_GENERATED' => 'AI Generated',
                        'IMPORTED' => 'Imported',
                        'NEEDS_REVISION' => 'Needs Revision',
                        'ARCHIVED' => 'Archived',
                    ]),
                Tables\Filters\SelectFilter::make('editorial_difficulty')
                    ->options([
                        'EASY' => 'Easy',
                        'MEDIUM' => 'Medium',
                        'HARD' => 'Hard',
                        'EXPERT' => 'Expert',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve_publish')
                    ->label('Approve & Publish')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn($record) => in_array($record->status, ['UNDER_REVIEW', 'AI_GENERATED', 'IMPORTED', 'DRAFT']))
                    ->action(function ($record) {
                        app(ContentPipelineService::class)->approveContent(
                            $record,
                            Auth::user(),
                            true,
                            5,
                            5,
                            'Approved and published via Content Studio'
                        );
                        Notification::make()->title('Question approved and published.')->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestionBanks::route('/'),
            'create' => Pages\CreateQuestionBank::route('/create'),
            'edit' => Pages\EditQuestionBank::route('/{record}/edit'),
        ];
    }
}
