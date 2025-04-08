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

        <div class="text-center" style="margin-top: -20px;">
            <img src="img/fln-logo.png" width="120px" alt="">
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Monitoring Kendaraan</h2>
            <span style="white-space: nowrap;">
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </span>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
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
                        {{ $loop->iteration }}
                    </td>
                    <td>{{ $k->nama_mobil }}</td>
                    <td>
                        <img src="{{ asset($k->image_path) }}" style="height: 100px; width: 100px;">
                    </td>
                    <td>{{ $k->nopol }}</td>
                    <td>
                        @php
                        $jam = \Illuminate\Support\Carbon::parse($k->updated_at)->timezone('Asia/Jakarta')->format('H:i');
                        @endphp

                        @if($k->status == 'Stand By')
                        <span class="badge bg-success">Stand By</span>
                        Jam {{ $jam }}
                        @elseif($k->status == 'Pergi')
                        <span class="badge bg-warning">Pergi</span>
                        Jam {{ $jam }}
                        @elseif($k->status == 'Perbaikan')
                        <span class="badge bg-danger">Perbaikan</span>
                        Jam {{ $jam }}
                        @else
                        <span class="badge bg-secondary">Status Tidak Dikenal</span>
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

                    response.forEach(function(k, index) {

                        let statusBadge = '';

                        const jam = new Date(k.updated_at).toLocaleTimeString('id-ID', {
                            hour: '2-digit'
                            , minute: '2-digit'
                            , timeZone: 'Asia/Jakarta'
                        });

                        if (k.status === 'Stand By') {
                            statusBadge = `<span class="badge bg-success">Stand By</span> Jam ${jam}`;
                        } else if (k.status === 'Pergi') {
                            statusBadge = `<span class="badge bg-warning">Pergi</span> Jam ${jam}`;
                        } else if (k.status === 'Perbaikan') {
                            statusBadge = `<span class="badge bg-danger">Perbaikan</span> Jam ${jam}`;
                        } else {
                            throw new Error(`Status tidak dikenal: ${k.status}`);
                        }

                        let row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${k.nama_mobil}</td>
                        <td><img src="${k.image_path}" style="height: 100px; width: 100px; object-fit: cover;"></td>
                        <td>${k.nopol}</td>
                        <td>${statusBadge}</td>
                        <td>${(k.nama_pemakai && k.departemen) ? `${k.nama_pemakai}<br>${k.departemen}` : '-'}</td>
                        <td>${k.driver || '-'}</td>
                        <td>${k.tujuan || '-'}</td>
                        <td>${k.keterangan || '-'}</td>
                    </tr>
                    `;

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
