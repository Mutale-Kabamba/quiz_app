<?php

namespace App\Filament\Resources\YouthUserResource\Pages;

use App\Filament\Resources\YouthUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListYouthUsers extends ListRecords
{
    protected static string $resource = YouthUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ Register Youth Member'),
        ];
    }
}
