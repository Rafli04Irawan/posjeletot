<?php

namespace App\Filament\Resources\TransaksiResource\Pages;

use App\Filament\Resources\TransaksiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Transaksi;

class ListTransaksis extends ListRecords
{
    protected static string $resource = TransaksiResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Actions\Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')

                ->action(function () {

                    $query = Transaksi::with('details.produk','outlet');

                    if(auth()->user()->role == 'pegawai'){
                        $query->where(
                            'outlet_id',
                            auth()->user()->outlet_id
                        );
                    }

                    $transaksi = $query->latest()->get();

                    $pdf = Pdf::loadView(
                        'pdf.laporan-transaksi',
                        compact('transaksi')
                    );

                    return response()->streamDownload(
                        fn()=>print($pdf->output()),
                        'Laporan-Transaksi.pdf'
                    );

                }),

        ];
    }
}
