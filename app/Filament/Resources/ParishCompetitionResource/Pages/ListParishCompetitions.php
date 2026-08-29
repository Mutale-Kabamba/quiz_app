<?php

namespace App\Filament\Resources\ParishCompetitionResource\Pages;

use App\Filament\Resources\ParishCompetitionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParishCompetitions extends ListRecords
{
    protected static string $resource = ParishCompetitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ Schedule Parish Competition'),
        ];
    }
}
