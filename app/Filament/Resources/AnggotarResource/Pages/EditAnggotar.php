<?php

namespace App\Filament\Resources\AnggotarResource\Pages;

use App\Filament\Resources\AnggotarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnggotar extends EditRecord
{
    protected static string $resource = AnggotarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
