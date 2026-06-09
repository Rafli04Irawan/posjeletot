<?php

namespace App\Filament\Widgets;

use App\Models\DetailTransaksi;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ProdukTerlarisWidget extends BaseWidget
{
    protected static ?string $heading = 'Produk Terlaris Minggu Ini';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DetailTransaksi::query()
                    ->selectRaw('produk_id, SUM(qty) as total_terjual, MAX(id) as id')
                    ->whereHas('transaksi', function (Builder $query) {
                        $query->whereBetween('created_at', [
                            now()->startOfWeek(),
                            now()->endOfWeek(),
                        ]);
                    })
                    ->with('produk')
                    ->groupBy('produk_id')
                    ->orderByDesc('total_terjual')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('produk.nama_produk')
                    ->label('Nama Produk'),

                Tables\Columns\TextColumn::make('total_terjual')
                    ->label('Terjual')
                    ->suffix(' pcs')
                    ->sortable(),
            ]);
    }
}