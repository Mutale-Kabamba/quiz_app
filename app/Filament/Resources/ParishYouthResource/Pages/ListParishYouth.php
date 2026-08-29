<?php

namespace App\Filament\Resources\ParishYouthResource\Pages;

use App\Filament\Resources\ParishYouthResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParishYouth extends ListRecords
{
    protected static string $resource = ParishYouthResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ Add Parish Youth'),
        ];
    }
}
