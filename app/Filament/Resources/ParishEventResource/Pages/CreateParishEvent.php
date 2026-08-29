<?php

namespace App\Filament\Resources\ParishEventResource\Pages;

use App\Filament\Resources\ParishEventResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateParishEvent extends CreateRecord
{
    protected static string $resource = ParishEventResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $admin = Auth::user();
        $data['parish_id'] = $admin->parish_id;
        $data['created_by'] = $admin->id;

        return $data;
    }
}
