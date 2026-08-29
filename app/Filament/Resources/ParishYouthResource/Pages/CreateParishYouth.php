<?php

namespace App\Filament\Resources\ParishYouthResource\Pages;

use App\Filament\Resources\ParishYouthResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateParishYouth extends CreateRecord
{
    protected static string $resource = ParishYouthResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $admin = Auth::user();

        // Strict Server Enforcement: Forces parish assignment to logged-in admin's parish
        $data['parish_id'] = $admin->parish_id;
        $data['role'] = 'youth';
        $data['status'] = 'approved';
        $data['approved_by'] = $admin->id;
        $data['approved_at'] = now();
        $data['xp'] = 0;
        $data['level'] = 1;
        $data['current_streak'] = 0;
        $data['longest_streak'] = 0;

        return $data;
    }
}
