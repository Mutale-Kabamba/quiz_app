<?php

namespace App\Filament\Resources\DeaneryResource\Pages;

use App\Filament\Resources\DeaneryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeaneries extends ListRecords
{
    protected static string $resource = DeaneryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ Add Deanery'),
        ];
    }
}
