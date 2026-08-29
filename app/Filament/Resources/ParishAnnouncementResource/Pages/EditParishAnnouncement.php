<?php

namespace App\Filament\Resources\ParishAnnouncementResource\Pages;

use App\Filament\Resources\ParishAnnouncementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditParishAnnouncement extends EditRecord
{
    protected static string $resource = ParishAnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
