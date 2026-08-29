<?php

namespace App\Filament\Resources\DeaneryResource\Pages;

use App\Filament\Resources\DeaneryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeanery extends EditRecord
{
    protected static string $resource = DeaneryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
