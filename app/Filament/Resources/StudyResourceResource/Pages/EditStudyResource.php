<?php

namespace App\Filament\Resources\StudyResourceResource\Pages;

use App\Filament\Resources\StudyResourceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudyResource extends EditRecord
{
    protected static string $resource = StudyResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
