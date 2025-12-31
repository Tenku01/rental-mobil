<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 16px; font-weight: bold; }
        .header p { margin: 2px 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #444; padding: 6px; text-align: left; }
        th { background-color: #eee; }
        .text-right { text-align: right; }
        .footer { margin-top: 30px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN TRANSAKSI RENTAL MOBIL</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tgl Transaksi</th>
                <th width="20%">Penyewa</th>
                <th width="20%">Armada</th>
                <th width="15%">Status</th>
                <th width="25%" class="text-right">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($row->tanggal_sewa)->format('d/m/Y') }}<br>
                    <small>s/d {{ \Carbon\Carbon::parse($row->tanggal_kembali)->format('d/m/Y') }}</small>
                </td>
                <td>
                    {{ $row->user->name ?? '-' }}<br>
                    <small style="color: #666;">{{ $row->user->email ?? '' }}</small>
                </td>
                <td>
                    {{ $row->mobil->merek ?? '-' }} {{ $row->mobil->tipe ?? '' }}<br>
                    <small>({{ $row->mobil->id ?? '-' }})</small>
                </td>
                <td>{{ ucfirst($row->status) }}</td>
                <td class="text-right">Rp {{ number_format($row->total_harga, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">Tidak ada data pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td colspan="5" class="text-right">TOTAL PENDAPATAN</td>
                <td class="text-right">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d F Y H:i') }}</p>
        <br><br>
        <p>( Administrator )</p>
    </div>
</body>
</html>