<!DOCTYPE html>
<html>

<head>
    <style>

        body{
            font-family: sans-serif;
            font-size:12px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        table,th,td{
            border:1px solid black;
        }

        th,td{
            padding:6px;
        }

        h2{
            text-align:center;
        }

    </style>
</head>

<body>

<h2>LAPORAN PENJUALAN</h2>

<table>

<tr>

<th>No</th>

<th>Tanggal</th>

<th>Total</th>

<th>Metode</th>

<th>Outlet</th>

</tr>

@foreach($transaksi as $item)

<tr>

<td>{{ $loop->iteration }}</td>

<td>{{ $item->created_at->format('d-m-Y') }}</td>

<td>Rp {{ number_format($item->total) }}</td>

<td>{{ $item->metode_pembayaran }}</td>

<td>{{ $item->outlet->nama_outlet }}</td>

</tr>

@endforeach

</table>

<br>

<b>Total Penjualan :
Rp {{ number_format($transaksi->sum('total')) }}</b>

</body>

</html>