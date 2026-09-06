<?php

namespace App\Filament\Resources\LaporanOutletResource\Pages;

use App\Filament\Resources\LaporanOutletResource;
use Filament\Resources\Pages\ListRecords;

class ListLaporanOutlets extends ListRecords
{
    protected static string $resource = LaporanOutletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
