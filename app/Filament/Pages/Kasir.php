<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Support\Facades\DB;

class Kasir extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static string $view = 'filament.pages.kasir';
    protected static ?string $navigationLabel = 'Kasir';
    protected static ?string $navigationGroup = 'Transaksi';

    public $produk = [];
    public $cart = [];
    public $bayar = 0;
    public $bayarFormatted = '';
    public $kembalian = 0;
    public $total = 0;
    public $showModalBayar = false;
    public $metodePembayaran = null;

    public function mount()
    {
       $user = auth()->user();

        if ($user->role === 'admin') {
            $this->produk = Produk::with('kategori')->get();
        } else {
            $this->produk = Produk::with('kategori')
                ->where('outlet_id', $user->outlet_id)
                ->get();
    }
    }

    public function tambahKeCart($id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return;
        }

        if ($produk->stok <= 0) {
            session()->flash('error', 'Stok produk habis!');
            return;
        }

        $qtySekarang = $this->cart[$id]['qty'] ?? 0;

        if ($qtySekarang >= $produk->stok) {
            session()->flash('error', 'Jumlah melebihi stok yang tersedia!');
            return;
        }

        if (isset($this->cart[$id])) {
            $this->cart[$id]['qty']++;
        } else {
            $this->cart[$id] = [
                'nama' => $produk->nama_produk,
                'harga' => $produk->harga,
                'qty' => 1,
            ];
        }

        $this->hitungTotal();
    }
    public function updatedBayarFormatted($value)
    {
        $angka = preg_replace('/[^0-9]/', '', $value);

        $this->bayar = (int) $angka;
        $this->bayarFormatted = $angka
            ? number_format($angka, 0, ',', '.')
            : '';

        $this->kembalian = max(0, $this->bayar - (int) $this->total);
    }
    public function kurangiQty($id)
    {
        if (isset($this->cart[$id])) {
            $this->cart[$id]['qty']--;

            if ($this->cart[$id]['qty'] <= 0) {
                unset($this->cart[$id]);
            }
        }

        $this->hitungTotal();
    }
    public function updateQty($id, $qty)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return;
        }

        $qty = (int) $qty;

        if ($qty <= 0) {
            unset($this->cart[$id]);
            $this->hitungTotal();
            return;
        }

        if ($qty > $produk->stok) {
            session()->flash('error', 'Jumlah melebihi stok yang tersedia!');
            $this->cart[$id]['qty'] = $produk->stok;
        } else {
            $this->cart[$id]['qty'] = $qty;
        }

        $this->hitungTotal();
    }

    public function hitungTotal()
    {
        $this->total = collect($this->cart)->sum(function ($item) {
            return (int) $item['harga'] * (int) $item['qty'];
        });

        $this->kembalian = max(0, (int) $this->bayar - (int) $this->total);
    }

    public function updatedBayar()
    {
        $this->kembalian = max(0, (int) $this->bayar - (int) $this->total);
    }
    public function pilihNominal($nominal)
    {
        $this->bayar = $nominal;
        $this->bayarFormatted = number_format($nominal, 0, ',', '.');
        $this->kembalian = max(0, (int) $this->bayar - (int) $this->total);
    }

    public function prosesBayar()
    {
        $bayar = (int) $this->bayar;
        $total = (int) $this->total;

        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang masih kosong!');
            return;
        }

        if (!$this->metodePembayaran) {
            session()->flash('error', 'Pilih metode pembayaran terlebih dahulu!');
            return;
        }

        if ($bayar < $total) {
            session()->flash('error', 'Uang kurang!');
            return;
        }

        try {
            DB::transaction(function () use ($bayar, $total) {
                $this->kembalian = $bayar - $total;

                $transaksi = Transaksi::create([
                    'total' => $total,
                    'bayar' => $bayar,
                    'outlet_id' => auth()->user()->outlet_id,
                    'kembalian' => $this->kembalian,
                    'metode_pembayaran' => $this->metodePembayaran,
                ]);

                foreach ($this->cart as $id => $item) {
                    $produk = Produk::find($id);

                    if (!$produk) {
                        throw new \Exception('Produk tidak ditemukan.');
                    }

                    if ($produk->stok < (int) $item['qty']) {
                        throw new \Exception('Stok ' . $produk->nama_produk . ' tidak mencukupi.');
                    }

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'produk_id' => $id,
                        'qty' => (int) $item['qty'],
                        'harga' => (int) $item['harga'],
                    ]);

                    $produk->decrement('stok', (int) $item['qty']);
                }
            });

            $this->resetTransaksi();
            $this->produk = Produk::with('kategori')->get();
            $this->showModalBayar = false;

            session()->flash('success', 'Transaksi berhasil! Stok produk berhasil dikurangi.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function resetTransaksi()
    {
        $this->cart = [];
        $this->total = 0;
        $this->bayar = 0;
        $this->kembalian = 0;
        $this->metodePembayaran = null;
    }

    public function bukaModal()
    {
        $this->showModalBayar = true;
    }

    public function pilihMetode($metode)
    {
        $this->metodePembayaran = $metode;

        $this->prosesBayar();
    }
    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'pegawai']);
    }
}