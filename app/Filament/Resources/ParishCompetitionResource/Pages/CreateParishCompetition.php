<?php

namespace App\Filament\Resources\ParishCompetitionResource\Pages;

use App\Filament\Resources\ParishCompetitionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateParishCompetition extends CreateRecord
{
    protected static string $resource = ParishCompetitionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $admin = Auth::user();
        $data['parish_id'] = $admin->parish_id;
        $data['created_by'] = $admin->id;

        return $data;
    }
}
