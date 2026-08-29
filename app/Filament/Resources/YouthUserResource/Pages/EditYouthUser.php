<?php

namespace App\Filament\Resources\YouthUserResource\Pages;

use App\Filament\Resources\YouthUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditYouthUser extends EditRecord
{
    protected static string $resource = YouthUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
