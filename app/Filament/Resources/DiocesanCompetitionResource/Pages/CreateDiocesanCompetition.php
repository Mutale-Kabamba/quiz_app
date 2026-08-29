<?php

namespace App\Filament\Resources\DiocesanCompetitionResource\Pages;

use App\Filament\Resources\DiocesanCompetitionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateDiocesanCompetition extends CreateRecord
{
    protected static string $resource = DiocesanCompetitionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();
        return $data;
    }
}
