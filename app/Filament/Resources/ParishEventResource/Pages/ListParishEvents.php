<?php

namespace App\Filament\Resources\ParishEventResource\Pages;

use App\Filament\Resources\ParishEventResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParishEvents extends ListRecords
{
    protected static string $resource = ParishEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ Create Parish Event'),
        ];
    }
}
