<?php

namespace App\Filament\Resources\DiocesanCompetitionResource\Pages;

use App\Filament\Resources\DiocesanCompetitionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDiocesanCompetition extends EditRecord
{
    protected static string $resource = DiocesanCompetitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
