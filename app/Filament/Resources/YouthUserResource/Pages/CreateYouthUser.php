<?php

namespace App\Filament\Resources\YouthUserResource\Pages;

use App\Filament\Resources\YouthUserResource;
use App\Services\AuditLogService;
use Filament\Resources\Pages\CreateRecord;

class CreateYouthUser extends CreateRecord
{
    protected static string $resource = YouthUserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = 'youth';
        $data['xp'] = 0;
        $data['level'] = 1;
        $data['current_streak'] = 0;
        $data['longest_streak'] = 0;

        return $data;
    }

    protected function afterCreate(): void
    {
        app(AuditLogService::class)->log(
            'youth_created_by_super_admin',
            $this->record,
            null,
            ['user_id' => $this->record->id, 'parish_id' => $this->record->parish_id]
        );
    }
}
