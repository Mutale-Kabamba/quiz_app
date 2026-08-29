<?php

namespace App\Filament\Resources\TaxonomyTrackResource\Pages;

use App\Filament\Resources\TaxonomyTrackResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTaxonomyTrack extends EditRecord
{
    protected static string $resource = TaxonomyTrackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
