<?php

namespace App\Filament\Resources\BahanBakuResource\Pages;

use App\Filament\Resources\BahanBakuResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBahanBaku extends CreateRecord
{
    protected static string $resource = BahanBakuResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['satuan'] == 'kg') {
            $data['stok'] = $data['stok'] * 1000;
            $data['satuan'] = 'gram';
        }

        return $data;
    }
    
}
