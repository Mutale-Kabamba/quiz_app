<?php

namespace App\Filament\Resources\ParishAdminResource\Pages;

use App\Filament\Resources\ParishAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParishAdmins extends ListRecords
{
    protected static string $resource = ParishAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ Add Parish Administrator'),
        ];
    }
}
