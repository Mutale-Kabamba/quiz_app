<?php

namespace App\Filament\Resources\DiocesanCompetitionResource\Pages;

use App\Filament\Resources\DiocesanCompetitionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDiocesanCompetitions extends ListRecords
{
    protected static string $resource = DiocesanCompetitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ Schedule Diocesan Competition'),
        ];
    }
}
