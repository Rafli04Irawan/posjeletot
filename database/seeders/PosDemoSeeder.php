<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
use App\Models\BillOfMaterial;
use App\Models\DetailTransaksi;
use App\Models\Kategori;
use App\Models\Outlet;
use App\Models\Produksi;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class PosDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $this->truncateTables([
            'detail_transaksi',
            'transaksi',
            'bill_of_materials',
            'produksis',
            'produk',
            'bahan_bakus',
            'kategori',
            'users',
            'outlets',
        ]);

        Schema::enableForeignKeyConstraints();

        $outlets = collect([
            ['nama_outlet' => 'Outlet Pusat', 'alamat' => 'Jl. Merdeka No. 1'],
            ['nama_outlet' => 'Outlet Cabang 1', 'alamat' => 'Jl. Sudirman No. 10'],
            ['nama_outlet' => 'Outlet Cabang 2', 'alamat' => 'Jl. Asia Afrika No. 22'],
        ])->map(function (array $data) {
            return Outlet::create($data);
        });

        $categories = collect([
            ['nama_kategori' => 'Cireng'],
            ['nama_kategori' => 'Gehu'],
        ])->map(function (array $data) {
            return Kategori::create($data);
        });

        $bahanBaku = collect([
            ['nama_bahan' => 'Tepung Tapioka', 'satuan' => 'kg', 'stok' => 50, 'outlet_id' => $outlets[0]->id],
            ['nama_bahan' => 'Keju', 'satuan' => 'gram', 'stok' => 5000, 'outlet_id' => $outlets[0]->id],
            ['nama_bahan' => 'Cabai Merah', 'satuan' => 'gram', 'stok' => 3000, 'outlet_id' => $outlets[1]->id],
            ['nama_bahan' => 'Tahu', 'satuan' => 'kg', 'stok' => 40, 'outlet_id' => $outlets[1]->id],
            ['nama_bahan' => 'Minyak Goreng', 'satuan' => 'liter', 'stok' => 60, 'outlet_id' => $outlets[2]->id],
            ['nama_bahan' => 'Garam', 'satuan' => 'kg', 'stok' => 15, 'outlet_id' => $outlets[2]->id],
            ['nama_bahan' => 'Air', 'satuan' => 'liter', 'stok' => 200, 'outlet_id' => $outlets[0]->id],
        ])->map(function (array $data) {
            return BahanBaku::create($data);
        });

        $products = collect([
            [
                'nama_produk' => 'Cireng Keju',
                'deskripsi' => 'Cireng renyah dengan keju leleh.',
                'harga' => 8000,
                'stok' => 40,
                'kategori_id' => $categories[0]->id,
                'gambar' => null,
                'outlet_id' => $outlets[0]->id,
            ],
            [
                'nama_produk' => 'Cireng Pedas',
                'deskripsi' => 'Cireng pedas dengan sambal cabai.',
                'harga' => 7500,
                'stok' => 35,
                'kategori_id' => $categories[0]->id,
                'gambar' => null,
                'outlet_id' => $outlets[1]->id,
            ],
            [
                'nama_produk' => 'Gehu Pedas',
                'deskripsi' => 'Gehu tahu pedas dengan bumbu rumah.',
                'harga' => 9000,
                'stok' => 30,
                'kategori_id' => $categories[1]->id,
                'gambar' => null,
                'outlet_id' => $outlets[2]->id,
            ],
        ])->map(function (array $data) {
            return Produk::create($data);
        });

        User::create([
            'name' => 'Admin POS',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'outlet_id' => $outlets[0]->id,
        ]);

        User::create([
            'name' => 'Pegawai Cabang 1',
            'email' => 'pegawai1@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'pegawai',
            'outlet_id' => $outlets[1]->id,
        ]);

        User::create([
            'name' => 'Pegawai Cabang 2',
            'email' => 'pegawai2@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'pegawai',
            'outlet_id' => $outlets[2]->id,
        ]);

        $bomData = [
            ['produk_id' => $products[0]->id, 'bahan_baku_id' => $bahanBaku[0]->id, 'jumlah' => 170, 'satuan' => 'gram'],
            ['produk_id' => $products[0]->id, 'bahan_baku_id' => $bahanBaku[1]->id, 'jumlah' => 40, 'satuan' => 'gram'],
            ['produk_id' => $products[0]->id, 'bahan_baku_id' => $bahanBaku[4]->id, 'jumlah' => 15, 'satuan' => 'ml'],
            ['produk_id' => $products[0]->id, 'bahan_baku_id' => $bahanBaku[5]->id, 'jumlah' => 3, 'satuan' => 'gram'],

            ['produk_id' => $products[1]->id, 'bahan_baku_id' => $bahanBaku[0]->id, 'jumlah' => 170, 'satuan' => 'gram'],
            ['produk_id' => $products[1]->id, 'bahan_baku_id' => $bahanBaku[2]->id, 'jumlah' => 25, 'satuan' => 'gram'],
            ['produk_id' => $products[1]->id, 'bahan_baku_id' => $bahanBaku[4]->id, 'jumlah' => 15, 'satuan' => 'ml'],
            ['produk_id' => $products[1]->id, 'bahan_baku_id' => $bahanBaku[5]->id, 'jumlah' => 3, 'satuan' => 'gram'],

            ['produk_id' => $products[2]->id, 'bahan_baku_id' => $bahanBaku[3]->id, 'jumlah' => 140, 'satuan' => 'gram'],
            ['produk_id' => $products[2]->id, 'bahan_baku_id' => $bahanBaku[2]->id, 'jumlah' => 25, 'satuan' => 'gram'],
            ['produk_id' => $products[2]->id, 'bahan_baku_id' => $bahanBaku[4]->id, 'jumlah' => 15, 'satuan' => 'ml'],
            ['produk_id' => $products[2]->id, 'bahan_baku_id' => $bahanBaku[5]->id, 'jumlah' => 3, 'satuan' => 'gram'],
        ];

        foreach ($bomData as $item) {
            BillOfMaterial::create($item);
        }

        Produksi::create(['produk_id' => $products[0]->id, 'jumlah' => 12, 'outlet_id' => $outlets[0]->id]);
        Produksi::create(['produk_id' => $products[1]->id, 'jumlah' => 10, 'outlet_id' => $outlets[1]->id]);
        Produksi::create(['produk_id' => $products[2]->id, 'jumlah' => 8, 'outlet_id' => $outlets[2]->id]);

        $transactions = [
            ['outlet_id' => $outlets[0]->id, 'metode_pembayaran' => 'cash', 'items' => [[$products[0]->id, 2, 8000], [$products[2]->id, 1, 9000]]],
            ['outlet_id' => $outlets[1]->id, 'metode_pembayaran' => 'qris', 'items' => [[$products[1]->id, 3, 7500]]],
            ['outlet_id' => $outlets[2]->id, 'metode_pembayaran' => 'cash', 'items' => [[$products[2]->id, 2, 9000], [$products[0]->id, 1, 8000]]],
            ['outlet_id' => $outlets[0]->id, 'metode_pembayaran' => 'transfer', 'items' => [[$products[1]->id, 1, 7500], [$products[2]->id, 1, 9000]]],
        ];

        foreach ($transactions as $transactionData) {
            $total = collect($transactionData['items'])->sum(fn ($item) => $item[1] * $item[2]);
            $bayar = $total + 3000;
            $kembalian = $bayar - $total;

            $transaksi = Transaksi::create([
                'total' => $total,
                'bayar' => $bayar,
                'kembalian' => $kembalian,
                'metode_pembayaran' => $transactionData['metode_pembayaran'],
                'outlet_id' => $transactionData['outlet_id'],
            ]);

            foreach ($transactionData['items'] as $item) {
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $item[0],
                    'qty' => $item[1],
                    'harga' => $item[2],
                ]);
            }
        }
    }

    protected function truncateTables(array $tables): void
    {
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
    }
}
