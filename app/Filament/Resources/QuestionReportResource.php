<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionReportResource\Pages;
use App\Models\Question;
use App\Models\QuestionReport;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class QuestionReportResource extends Resource
{
    protected static ?string $model = QuestionReport::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-flag';
    protected static string | UnitEnum | null $navigationGroup = 'Content Issues';
    protected static ?string $navigationLabel = 'Reported Content Issues';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        // Chairperson sees only their own submitted reports
        if ($user && $user->role === 'chairperson') {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('question_id')
                    ->label('Target Question')
                    ->options(Question::pluck('question_text', 'id'))
                    ->searchable()
                    ->required()
                    ->columnSpanFull(),

                Select::make('issue_type')
                    ->options([
                        'wrong_answer' => 'Wrong Answer Key Assigned',
                        'typo' => 'Typographical Error in Question / Options',
                        'bad_reference' => 'Incorrect Scripture / CCC / YOUCAT Citation',
                        'inappropriate' => 'Inappropriate Content',
                        'other' => 'Other Concern',
                    ])
                    ->required(),

                Textarea::make('notes')
                    ->label('Explanation / Suggested Correction')
                    ->rows(3)
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question.question_text')
                    ->label('Question')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('issue_type')
                    ->badge()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Reported By')
                    ->sortable()
                    ->visible(fn () => Auth::user()?->role === 'super_admin'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'under_review' => 'primary',
                        'resolved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'under_review' => 'Under Review',
                        'resolved' => 'Resolved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Action::make('resolve')
                    ->label('Resolve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (QuestionReport $record) => Auth::user()?->role === 'super_admin' && $record->status !== 'resolved')
                    ->action(function (QuestionReport $record) {
                        $record->update(['status' => 'resolved']);
                        Notification::make()->title('Issue Marked as Resolved')->success()->send();
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
            'index' => Pages\ListQuestionReports::route('/'),
            'create' => Pages\CreateQuestionReport::route('/create'),
        ];
    }
}
