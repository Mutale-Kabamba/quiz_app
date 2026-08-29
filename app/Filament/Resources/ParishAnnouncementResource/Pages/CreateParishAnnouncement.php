<?php

namespace App\Filament\Resources\ParishAnnouncementResource\Pages;

use App\Filament\Resources\ParishAnnouncementResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateParishAnnouncement extends CreateRecord
{
    protected static string $resource = ParishAnnouncementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $admin = Auth::user();
        $data['parish_id'] = $admin->parish_id;
        $data['created_by'] = $admin->id;
        $data['sent_at'] = now();

        return $data;
    }
}
