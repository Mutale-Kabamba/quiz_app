<?php

namespace App\Filament\Resources\ParishAnnouncementResource\Pages;

use App\Filament\Resources\ParishAnnouncementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParishAnnouncements extends ListRecords
{
    protected static string $resource = ParishAnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ Dispatch Announcement'),
        ];
    }
}
