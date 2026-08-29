<?php

namespace App\Filament\Resources\SaintProfileResource\Pages;

use App\Filament\Resources\SaintProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSaintProfile extends EditRecord
{
    protected static string $resource = SaintProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
