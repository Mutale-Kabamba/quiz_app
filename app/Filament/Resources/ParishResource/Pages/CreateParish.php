<?php

namespace App\Filament\Resources\ParishResource\Pages;

use App\Filament\Resources\ParishResource;
use App\Services\AuditLogService;
use Filament\Resources\Pages\CreateRecord;

class CreateParish extends CreateRecord
{
    protected static string $resource = ParishResource::class;

    protected function afterCreate(): void
    {
        app(AuditLogService::class)->log(
            'parish_created',
            $this->record,
            null,
            $this->record->toArray()
        );
    }
}
