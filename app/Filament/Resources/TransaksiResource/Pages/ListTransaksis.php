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

                    $nomorSurat = 'LAP-TRX/' . now()->format('YmdHis');
                    $tanggalLaporan = now()->format('d F Y');
                    $periode = $transaksi->count()
                        ? $transaksi->min('created_at')->format('d M Y') . ' s/d ' . $transaksi->max('created_at')->format('d M Y')
                        : 'Semua Periode';

                    $view = auth()->user()->role === 'admin'
                        ? 'pdf.laporan-transaksi-admin'
                        : 'pdf.laporan-transaksi';

                    if (auth()->user()->role === 'admin') {
                        $summary = $transaksi
                            ->groupBy(function ($item) {
                                return ($item->outlet?->nama_outlet ?? 'Outlet Tidak Diketahui') . '||' . $item->created_at->format('Y-m-d');
                            })
                            ->map(function ($items, $key) {
                                [$outlet, $date] = explode('||', $key);

                                return [
                                    'outlet' => $outlet,
                                    'tanggal' => $items->first()->created_at->format('d-m-Y'),
                                    'total' => $items->sum('total'),
                                    'jumlah_transaksi' => $items->count(),
                                ];
                            })
                            ->values();

                        $pdf = Pdf::loadView(
                            $view,
                            compact('summary', 'nomorSurat', 'tanggalLaporan', 'periode')
                        );
                    } else {
                        $pdf = Pdf::loadView(
                            $view,
                            compact('transaksi', 'nomorSurat', 'tanggalLaporan', 'periode')
                        );
                    }

                    return response()->streamDownload(
                        fn() => print($pdf->output()),
                        'Laporan-Transaksi.pdf'
                    );

                }),

        ];
    }
}
