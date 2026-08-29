<?php

namespace App\Filament\Resources\QuestionReportResource\Pages;

use App\Filament\Resources\QuestionReportResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateQuestionReport extends CreateRecord
{
    protected static string $resource = QuestionReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';

        return $data;
    }
}
