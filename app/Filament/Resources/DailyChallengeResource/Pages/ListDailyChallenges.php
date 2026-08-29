<?php

namespace App\Filament\Resources\DailyChallengeResource\Pages;

use App\Filament\Resources\DailyChallengeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDailyChallenges extends ListRecords
{
    protected static string $resource = DailyChallengeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
