<?php

namespace App\Filament\Widgets;

use App\Models\DetailTransaksi;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ProdukTerlarisHarianWidget extends BaseWidget
{
    protected static ?string $heading = 'Produk Terlaris Hari Ini per Outlet';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DetailTransaksi::query()
                    ->join('transaksi', 'transaksi.id', '=', 'detail_transaksi.transaksi_id')
                    ->selectRaw('MAX(detail_transaksi.id) as id, MAX(detail_transaksi.transaksi_id) as transaksi_id, detail_transaksi.produk_id, transaksi.outlet_id, SUM(detail_transaksi.qty) as total_terjual')
                    ->whereDate('transaksi.created_at', today())
                    ->with(['produk', 'transaksi.outlet'])
                    ->groupBy('detail_transaksi.produk_id', 'transaksi.outlet_id')
                    ->orderByDesc('total_terjual')
            )
            ->columns([
                Tables\Columns\TextColumn::make('transaksi.outlet.nama_outlet')
                    ->label('Outlet')
                    ->sortable(),

                Tables\Columns\TextColumn::make('produk.nama_produk')
                    ->label('Nama Produk'),

                Tables\Columns\TextColumn::make('total_terjual')
                    ->label('Terjual')
                    ->suffix(' pcs')
                    ->sortable(),
            ])
            ->groups([
                Group::make('outlet_id')
                    ->label('Outlet')
                    ->getTitleFromRecordUsing(fn (DetailTransaksi $record): string => $record->transaksi?->outlet?->nama_outlet ?? 'Tanpa Outlet')
                    ->collapsible(),
            ]);
    }
}
