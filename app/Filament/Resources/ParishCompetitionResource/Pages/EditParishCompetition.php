<?php

namespace App\Filament\Resources\ParishCompetitionResource\Pages;

use App\Filament\Resources\ParishCompetitionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditParishCompetition extends EditRecord
{
    protected static string $resource = ParishCompetitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
