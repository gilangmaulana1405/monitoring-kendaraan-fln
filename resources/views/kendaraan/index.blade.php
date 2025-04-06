<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                    <th>Driver</th>
                    <th>Tujuan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody id="kendaraanTable">
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
                        Jam {{ \Illuminate\Support\Carbon::parse($k->updated_at)->timezone('Asia/Jakarta')->format('H:i') }}
                        @endif
                    </td>
                    <td>{{ $k->nama_pemakai }} <br> {{ $k->departemen }} </td>
                    <td>{{ $k->driver }}</td>
                    <td>{{ $k->tujuan }}</td>
                    <td>{{ $k->keterangan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        function fetchData() {
            $.ajax({
                url: '/kendaraan/data'
                , type: 'GET'
                , dataType: 'json'
                , success: function(response) {
                    let tableBody = $('#kendaraanTable');
                    tableBody.empty();
                    response.forEach(function(k) {
                        let statusBadge = k.status === 'Stand By' ?
                            `<span class="badge bg-success">Stand By</span>` :
                            `<span class="badge bg-warning">Pergi</span> Jam ${new Date(k.updated_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}`;

                        let row = `
                            <tr>
                                <td>${new Date().toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' })}</td>
                                <td>${k.nama_mobil}</td>
                                <td><img src="${k.image_path}" style="height: 100px; width: 100px; object-fit: cover;"></td>
                                <td>${k.nopol}</td>
                                <td>${statusBadge}</td>
                                <td>${(k.nama_pemakai && k.departemen) ? `${k.nama_pemakai}<br>${k.departemen}` : '-'}</td>
                                <td>${k.driver || '-'}</td>
                                <td>${k.tujuan || '-'}</td>
                                <td>${k.keterangan || '-'}</td>
                            </tr>`;
                        tableBody.append(row);
                    });
                }
                , error: function() {
                    console.log('Gagal mengambil data.');
                }
            });
        }

        setInterval(fetchData, 1000);

    </script>


</body>
</html>
