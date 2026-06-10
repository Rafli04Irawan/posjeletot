<?php

namespace App\Filament\Resources\ProduksiResource\Pages;

use App\Filament\Resources\ProduksiResource;
use App\Models\Produk;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateProduksi extends CreateRecord
{
    protected static string $resource = ProduksiResource::class;

    protected function beforeCreate(): void
    {
        $produk = Produk::with('billOfMaterials.bahanBaku')
            ->find($this->data['produk_id']);

        $jumlahProduksi = (int) $this->data['jumlah'];

        if (!$produk) {
            Notification::make()
                ->title('Produk tidak ditemukan')
                ->danger()
                ->send();

            $this->halt();
        }

        if ($produk->billOfMaterials->isEmpty()) {
            Notification::make()
                ->title('BOM produk belum dibuat')
                ->body('Tambahkan Bill of Material terlebih dahulu.')
                ->danger()
                ->send();

            $this->halt();
        }

        foreach ($produk->billOfMaterials as $bom) {
            $kebutuhan = $bom->jumlah * $jumlahProduksi;
            $stokBahan = $bom->bahanBaku->stok ?? 0;

            if ($stokBahan < $kebutuhan) {
                Notification::make()
                    ->title('Stok bahan tidak cukup')
                    ->body($bom->bahanBaku->nama_bahan . ' kurang. Butuh ' . number_format($kebutuhan, 0, ',', '.') . ' ' . $bom->satuan)
                    ->danger()
                    ->send();

                $this->halt();
            }
        }
    }

    protected function afterCreate(): void
    {
        DB::transaction(function () {
            $produk = Produk::with('billOfMaterials.bahanBaku')
                ->find($this->record->produk_id);

            $jumlahProduksi = (int) $this->record->jumlah;

            foreach ($produk->billOfMaterials as $bom) {
                $kebutuhan = $bom->jumlah * $jumlahProduksi;

                $bom->bahanBaku->decrement('stok', $kebutuhan);
            }

            $produk->increment('stok', $jumlahProduksi);
        });

        Notification::make()
            ->title('Produksi berhasil')
            ->body('Stok produk bertambah dan bahan baku otomatis berkurang.')
            ->success()
            ->send();
    }
}