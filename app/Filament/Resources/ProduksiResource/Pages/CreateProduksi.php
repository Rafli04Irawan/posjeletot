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

        $stokBahan = $bom->bahanBaku->stok;

        /*
        |-----------------------------
        | Konversi satuan stok
        |-----------------------------
        */

        if ($bom->bahanBaku->satuan == 'kg' && $bom->satuan == 'gram') {
            $stokBahan *= 1000;
        }

        if ($bom->bahanBaku->satuan == 'liter' && $bom->satuan == 'ml') {
            $stokBahan *= 1000;
        }

        if ($bom->bahanBaku->satuan == 'gram' && $bom->satuan == 'kg') {
            $stokBahan /= 1000;
        }

        if ($bom->bahanBaku->satuan == 'ml' && $bom->satuan == 'liter') {
            $stokBahan /= 1000;
        }

        if ($stokBahan < $kebutuhan) {

            Notification::make()
                ->title('Stok bahan tidak cukup')
                ->body(
                    $bom->bahanBaku->nama_bahan .
                    ' kurang. Dibutuhkan ' .
                    number_format($kebutuhan,0,',','.') .
                    ' ' . $bom->satuan
                )
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

            $bahan = $bom->bahanBaku;

            if ($bahan->satuan == 'kg' && $bom->satuan == 'gram') {

                $stokGram = ($bahan->stok * 1000) - $kebutuhan;

                $bahan->update([
                    'stok' => $stokGram / 1000
                ]);

            } elseif ($bahan->satuan == 'liter' && $bom->satuan == 'ml') {

                $stokMl = ($bahan->stok * 1000) - $kebutuhan;

                $bahan->update([
                    'stok' => $stokMl / 1000
                ]);

            } else {

                $bahan->decrement('stok', $kebutuhan);

            }
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