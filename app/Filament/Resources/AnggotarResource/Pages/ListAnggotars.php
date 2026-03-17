<?php

namespace App\Filament\Resources\AnggotarResource\Pages;

use App\Filament\Resources\AnggotarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAnggotars extends ListRecords
{
    protected static string $resource = AnggotarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
