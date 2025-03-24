<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="10">
    <title>Monitoring Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">

        <div class="d-flex flex-column align-items-center">
            <img src="img/fln-logo.png" class="mb-4" width="120px" style="margin-top: -20px;" alt="">
            <h2 class="mb-4">Monitoring Kendaraan</h2>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Mobil</th>
                    <th width="100">Gambar</th>
                    <th>No Polisi</th>
                    <th>Status</th>
                    <th>Pemakai</th>
                    <th>Tujuan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kendaraan as $k)
                <tr>
                    <td>
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </td>
                    <td>{{ $k->nama_mobil }}</td>
                    <td>
                        <img src="{{ asset($k->image_path) }}" style="height: 100px; width: 100px;">
                    </td>
                    <td>{{ $k->nopol }}</td>
                    <td>
                        @if($k->status == 'Stand By')
                        <span class="badge bg-success">Stand By</span>
                        @else
                        <span class="badge bg-warning">Pergi</span>
                        Jam {{ \Illuminate\Support\Carbon::parse($k->updated_at)->timezone('Asia/Jakarta')->format('H:i:s') }}
                        @endif
                    </td>
                    <td>{{ $k->nama_pemakai }} - {{ $k->departemen }} </td>
                    <td>{{ $k->tujuan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
