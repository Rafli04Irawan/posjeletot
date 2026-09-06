<x-filament::page>

    <style>
        .kasir-wrapper {
            display: grid !important;
            grid-template-columns: 2fr 1fr !important;
            gap: 16px !important;
            width: 100% !important;
        }

        .produk-area {
            background: white !important;
            border-radius: 12px !important;
            padding: 16px !important;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1) !important;
        }

        .produk-grid-custom {
            display: grid !important;
            grid-template-columns: repeat(5, 1fr) !important;
            gap: 12px !important;
        }

        .produk-card-custom {
            border: 1px solid #e5e7eb !important;
            border-radius: 10px !important;
            padding: 8px !important;
            background: white !important;
            cursor: pointer !important;
        }

        .produk-img-box {
            width: 100% !important;
            height: 75px !important;
            overflow: hidden !important;
            border-radius: 8px !important;
            margin-bottom: 8px !important;
        }

        .produk-img-custom {
            width: 100% !important;
            height: 75px !important;
            max-height: 75px !important;
            object-fit: cover !important;
            display: block !important;
        }

        .keranjang-area {
            display: flex !important;
            flex-direction: column !important;
            gap: 16px !important;
        }

        @media (max-width: 1024px) {
            .produk-grid-custom {
                grid-template-columns: repeat(4, 1fr) !important;
            }
        }

        @media (max-width: 768px) {
            .kasir-wrapper {
                grid-template-columns: 1fr !important;
            }

            .produk-grid-custom {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }
    </style>

    <div class="kasir-wrapper">

        <!-- LEFT: PRODUK -->
        <div class="produk-area">

            <h2 style="font-size: 20px; font-weight: bold; margin-bottom: 16px;">
                Daftar Produk
            </h2>

            <div class="produk-grid-custom">
                @foreach ($this->produk as $item)
                    <div 
                        wire:click="tambahKeCart({{ $item->id }})"
                        class="produk-card-custom">

                        <div class="produk-img-box">
                            <img src="{{ asset('storage/' . $item->gambar) }}"
                                 class="produk-img-custom">
                        </div>

                        <p style="font-weight: 600; font-size: 13px; line-height: 1.2; margin: 0 0 4px 0;">
                            {{ $item->nama_produk }}
                        </p>

                        <p style="font-size: 12px; color: #6b7280; margin: 0 0 4px 0;">
                            {{ $item->kategori->nama_kategori ?? '-' }}
                        </p>

                        <p style="color: #2563eb; font-weight: bold; font-size: 13px; margin: 0 0 4px 0;">
                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                        </p>

                        @if ($item->stok > 0)
                        <p style="font-size: 12px; margin: 0; color: #16a34a;">
                        @else
                        <p style="font-size: 12px; margin: 0; color: #dc2626;">
                        @endif
                            Stok: {{ $item->stok }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- RIGHT: KERANJANG -->
        <div class="keranjang-area">

            <div style="background:white; border-radius:12px; padding:16px; box-shadow:0 1px 4px rgba(0,0,0,0.1); display:flex; flex-direction:column;">

                <h2 style="font-size:18px; font-weight:bold; margin-bottom:12px;">
                    🛒 Keranjang
                </h2>

                <div style="flex:1; overflow-y:auto;">
                    @forelse ($this->cart as $id => $item)
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; border-bottom:1px solid #e5e7eb; padding-bottom:8px;">
                            <div>
                                <p style="font-weight:600; font-size:14px; margin:0;">
                                    {{ $item['nama'] }}
                                </p>
                                <p style="font-size:12px; color:#6b7280; margin:0;">
                                    Rp {{ number_format($item['harga'], 0, ',', '.') }}
                                </p>
                            </div>

                           <div style="display:flex; align-items:center; gap:8px;">
                            <button
                                wire:click="kurangiQty({{ $id }})"
                                style="background:#ef4444;color:white;padding:4px 10px;border-radius:6px;">
                                -
                            </button>

                            <input
                                type="number"
                                min="1"
                                value="{{ $item['qty'] }}"
                                wire:change="updateQty({{ $id }}, $event.target.value)"
                                style="
                                    width:65px;
                                    text-align:center;
                                    border:1px solid #d1d5db;
                                    border-radius:6px;
                                    padding:4px;
                                "
                            >

                            <button
                                wire:click="tambahKeCart({{ $id }})"
                                style="background:#22c55e;color:white;padding:4px 10px;border-radius:6px;">
                                +
                            </button>

                        </div>
                        </div>
                    @empty
                        <p style="color:#9ca3af; text-align:center; margin-top:40px;">
                            Keranjang kosong
                        </p>
                    @endforelse
                </div>

                <div style="margin-top:16px; padding:12px; background:#eff6ff; border-radius:8px; text-align:center;">
                    <p style="font-size:14px; color:#4b5563; margin:0;">TOTAL</p>
                    <p style="font-size:24px; font-weight:bold; color:#2563eb; margin:0;">
                        Rp {{ number_format($this->total, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <div style="background:white; border-radius:12px; padding:16px; box-shadow:0 1px 4px rgba(0,0,0,0.1);">
                <input type="number"
                    wire:model.live="bayarFormatted"
                    placeholder="Masukkan uang"
                    style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px; margin-bottom:8px;">
            <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:8px; margin-bottom:12px;">
                <button
                    wire:click="pilihNominal(10000)"
                    type="button"
                    style="background:#f3f4f6; padding:8px; border-radius:6px; font-weight:600;">
                    10.000
                </button>

                <button
                    wire:click="pilihNominal(20000)"
                    type="button"
                    style="background:#f3f4f6; padding:8px; border-radius:6px; font-weight:600;">
                    20.000
                </button>

                <button
                    wire:click="pilihNominal(50000)"
                    type="button"
                    style="background:#f3f4f6; padding:8px; border-radius:6px; font-weight:600;">
                    50.000
                </button>

                <button
                    wire:click="pilihNominal(100000)"
                    type="button"
                    style="background:#f3f4f6; padding:8px; border-radius:6px; font-weight:600;">
                    100.000
                </button>

            </div>
                <div style="text-align:center; margin-bottom:12px;">
                    <p style="font-size:14px; color:#6b7280; margin:0;">Kembalian</p>
                    <p style="font-size:20px; font-weight:bold; color:#16a34a; margin:0;">
                        Rp {{ number_format($this->kembalian, 0, ',', '.') }}
                    </p>
                </div>

                <button wire:click="bukaModal"
                    style="background:#2563eb; color:white; width:100%; padding:12px; border-radius:8px; font-size:18px; font-weight:bold;">
                    BAYAR
                </button>

                <button wire:click="resetTransaksi"
                    style="background:#6b7280; color:white; width:100%; padding:8px; border-radius:6px; margin-top:8px;">
                    Reset
                </button>
            </div>

        </div>
    </div>

    @if ($showModalBayar)
        <div style="position:fixed; inset:0; display:flex; align-items:center; justify-content:center; z-index:50; background:rgba(0,0,0,0.5);">

            <div style="background:white; padding:24px; border-radius:12px; width:320px; text-align:center; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
                <h2 style="font-size:18px; font-weight:bold; margin-bottom:16px;">
                    Pilih Pembayaran
                </h2>

                <button wire:click="pilihMetode('cash')"
                    style="width:100%; padding:12px; margin-bottom:8px; border-radius:6px; color:white; background:#16a34a;">
                    💵 CASH
                </button>

                <button wire:click="pilihMetode('qris')"
                    @disabled($this->bayar > $this->total)
                    style="width:100%; padding:12px; margin-bottom:8px; border-radius:6px; color:white; background:#9333ea; opacity:{{ $this->bayar > $this->total ? '0.5' : '1' }}; cursor:{{ $this->bayar > $this->total ? 'not-allowed' : 'pointer' }};">
                    📱 QRIS
                </button>

                <button wire:click="$set('showModalBayar', false)"
                    style="width:100%; padding:8px; border-radius:6px; color:white; background:#9ca3af;">
                    BATAL
                </button>
            </div>
        </div>
    @endif

</x-filament::page>