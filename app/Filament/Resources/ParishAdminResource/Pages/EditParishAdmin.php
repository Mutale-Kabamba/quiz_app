<?php

namespace App\Filament\Resources\ParishAdminResource\Pages;

use App\Filament\Resources\ParishAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditParishAdmin extends EditRecord
{
    protected static string $resource = ParishAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
