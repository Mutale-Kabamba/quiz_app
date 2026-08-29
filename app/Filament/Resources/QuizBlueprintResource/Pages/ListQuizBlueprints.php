<?php

namespace App\Filament\Resources\QuizBlueprintResource\Pages;

use App\Filament\Resources\QuizBlueprintResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuizBlueprints extends ListRecords
{
    protected static string $resource = QuizBlueprintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
