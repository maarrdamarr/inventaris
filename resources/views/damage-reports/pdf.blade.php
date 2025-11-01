<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { margin: 0 0 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 6px; }
        th { background: #eee; }
    </style>
    <title>Laporan Kerusakan</title>
    </head>
<body>
    <h2>Laporan Kerusakan</h2>
    <table>
        <thead>
            <tr>
                <th>Judul</th>
                <th>Barang</th>
                <th>Pelapor</th>
                <th>Keparahan</th>
                <th>Status</th>
                <th>Dibuat</th>
            </tr>
        </thead>
        <tbody>
        @foreach($reports as $r)
            <tr>
                <td>{{ $r->title }}</td>
                <td>{{ $r->commodity->name ?? '-' }}</td>
                <td>{{ $r->reporter->name ?? '-' }}</td>
                <td>{{ ucfirst($r->severity) }}</td>
                <td>{{ ucfirst($r->status) }}</td>
                <td>{{ $r->created_at?->format('Y-m-d') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>

