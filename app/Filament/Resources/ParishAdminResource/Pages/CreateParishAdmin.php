<?php

namespace App\Filament\Resources\ParishAdminResource\Pages;

use App\Filament\Resources\ParishAdminResource;
use App\Services\AuditLogService;
use Filament\Resources\Pages\CreateRecord;

class CreateParishAdmin extends CreateRecord
{
    protected static string $resource = ParishAdminResource::class;

    protected function afterCreate(): void
    {
        app(AuditLogService::class)->log(
            'admin_created',
            $this->record,
            null,
            ['user_id' => $this->record->id, 'parish_id' => $this->record->parish_id, 'role' => $this->record->role]
        );
    }
}
