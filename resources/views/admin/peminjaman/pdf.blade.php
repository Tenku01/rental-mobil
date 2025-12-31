<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h2>
        Laporan Peminjaman Mobil 
        @if ($bulan)
            - Bulan {{ \Carbon\Carbon::create()->month($bulan)->format('F') }}
        @else
            - Semua Bulan
        @endif
    </h2>
    <table>
        <thead>
            <tr>
                <th>ID Peminjaman</th>
                <th>User ID</th>
                <th>Mobil ID</th>
                <th>Tanggal Sewa</th>
                <th>Tanggal Kembali</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($peminjaman as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->user_id }}</td>
                    <td>{{ $p->mobil_id }}</td>
                    <td>{{ $p->tanggal_sewa }}</td>
                    <td>{{ $p->tanggal_kembali }}</td>
                    <td>{{ $p->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
