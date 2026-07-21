<?php

namespace App\Filament\Resources\BahanBakuResource\Pages;

use App\Filament\Resources\BahanBakuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBahanBaku extends EditRecord
{
    protected static string $resource = BahanBakuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['satuan'] == 'kg') {
            $data['stok'] = $data['stok'] * 1000;
            $data['satuan'] = 'gram';
        }

        return $data;
    }
}
