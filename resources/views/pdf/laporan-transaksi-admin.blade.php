<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            letter-spacing: 1px;
        }

        .header .sub-title {
            margin-top: 4px;
            font-size: 12px;
        }

        .meta {
            margin-bottom: 20px;
            font-size: 12px;
        }

        .meta .row {
            margin-bottom: 4px;
        }

        .meta .label {
            width: 160px;
            display: inline-block;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        table,
        th,
        td {
            border: 1px solid #333;
        }

        th,
        td {
            padding: 8px;
            vertical-align: top;
            font-size: 11px;
        }

        th {
            background: #f3f3f3;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .summary {
            margin-top: 8px;
            font-size: 12px;
        }

        .summary strong {
            font-size: 13px;
        }
    </style>
</head>

<body>

<div class="header">
    <h1>Laporan Pendapatan Transaksi</h1>
    <div class="sub-title">Ringkasan transaksi cabang</div>
</div>

<div class="meta">
    <div class="row"><span class="label">Nomor Surat</span>: {{ $nomorSurat }}</div>
    <div class="row"><span class="label">Tanggal Laporan</span>: {{ $tanggalLaporan }}</div>
    <div class="row"><span class="label">Periode</span>: {{ $periode }}</div>
    <div class="row"><span class="label">Dicetak Oleh</span>: {{ auth()->user()->name }}</div>
</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Outlet</th>
            <th>Total Pendapatan</th>
            <th>Jumlah Transaksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($summary as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['tanggal'] }}</td>
                <td>{{ $item['outlet'] }}</td>
                <td>Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                <td>{{ $item['jumlah_transaksi'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="summary">
    <strong>Total Hari/Cabang:</strong> {{ $summary->count() }}<br>
    <strong>Total Pendapatan:</strong> Rp {{ number_format($summary->sum('total'), 0, ',', '.') }}
</div>

</body>

</html>
