<?php

namespace App\Filament\Resources\StudyResourceResource\Pages;

use App\Filament\Resources\StudyResourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudyResources extends ListRecords
{
    protected static string $resource = StudyResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
