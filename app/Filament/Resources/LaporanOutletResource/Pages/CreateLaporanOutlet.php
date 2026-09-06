<?php

namespace App\Filament\Resources\LaporanOutletResource\Pages;

use App\Filament\Resources\LaporanOutletResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLaporanOutlet extends CreateRecord
{
    protected static string $resource = LaporanOutletResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if ($user->role !== 'admin') {
            $data['outlet_id'] = $user->outlet_id;
        }

        $data['uploaded_by'] = $user->id;

        return $data;
    }
}
