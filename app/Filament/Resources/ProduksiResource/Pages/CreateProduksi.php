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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->user()->role == 'pegawai') {
            $data['outlet_id'] = auth()->user()->outlet_id;
        }

        return $data;
    }
    protected function beforeCreate(): void
    {
        $produk = Produk::find($this->data['produk_id']);
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

        $perhitungan = $produk->hitungProduksi($jumlahProduksi);
        if ($jumlahProduksi > $perhitungan['maksimal']) {
            $kekurangan = collect($perhitungan['kekurangan'])
                ->map(fn (array $item) => $item['nama'] . ' (butuh ' . number_format($item['dibutuhkan'], 0, ',', '.') . ' ' . $item['satuan'] . ', tersedia ' . number_format($item['tersedia'], 0, ',', '.') . ' ' . $item['satuan'] . ')')
                ->implode('; ');

            Notification::make()
                ->title('Stok bahan tidak cukup')
                ->body('Maksimal dapat dibuat ' . number_format($perhitungan['maksimal'], 0, ',', '.') . ' pcs. ' . $kekurangan)
                ->danger()
                ->send();

            $this->halt();
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

            $kebutuhanDalamStok = $kebutuhan;

            if ($bahan->satuan === 'kg' && $bom->satuan === 'gram') {
                $kebutuhanDalamStok = $kebutuhan / 1000;
            } elseif ($bahan->satuan === 'liter' && $bom->satuan === 'ml') {
                $kebutuhanDalamStok = $kebutuhan / 1000;
            } elseif ($bahan->satuan === 'gram' && $bom->satuan === 'kg') {
                $kebutuhanDalamStok = $kebutuhan * 1000;
            } elseif ($bahan->satuan === 'ml' && $bom->satuan === 'liter') {
                $kebutuhanDalamStok = $kebutuhan * 1000;
            }

            $bahan->decrement('stok', $kebutuhanDalamStok);
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