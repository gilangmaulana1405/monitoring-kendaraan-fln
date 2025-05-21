<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Kendaraan</title>

    {{-- datatables non cdn / offline --}}
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/jquery/jquery-3.6.0.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/datatables/css/jquery.dataTables.min.css') }}">
</head>

<body>
    <div class="container mt-5">
        <div class="text-center" style="margin-top: -20px;">
            <img src="img/fln-logo.png" width="120px" alt="">
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">List Kendaraan</h2>

            <div class="d-flex flex-column align-items-end">
                <span style="white-space: nowrap;">
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </span>

                <button class="btn btn-primary btn-sm mt-2">
                    Tambah Kendaraan
                </button>
            </div>
        </div>

        <div id="loading" style="text-align: center; margin: 30px 0; display: none;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
        </div>

        <div class="table-responsive">
            <table id="kendaraan-table" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Mobil</th>
                        <th>No Polisi</th>
                        <th>Gambar</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- DataTables non cdn offline JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/js/jquery.dataTables.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            const table = $('#kendaraan-table').DataTable({
                columns: [{
                        title: "No"
                        , width: "40px"
                    }
                    , {
                        title: "Nama Mobil"
                    }
                    , {
                        title: "No Polisi"
                    }
                    , {
                        title: "Gambar"
                    }
                    , {
                        title: "Action"
                    }
                ]
                , columnDefs: [{
                    targets: 0
                    , width: "40px"
                }]
                , pageLength: 10
            });

            let isFirstLoad = true; // Mengatur apakah ini pemuatan pertama kali

            function fetchData() {
                if (isFirstLoad) {
                    $('#loading').show(); // Menampilkan spinner hanya saat pemuatan pertama
                }

                $.ajax({
                    url: "{{ route('list.kendaraan.data') }}"
                    , type: 'GET'
                    , dataType: 'json'
                    , success: function(data) {
                        let tableData = [];

                        $.each(data, function(i, item) {
                            const gambarPath = item.gambar_mobil ?
                                `/img/mobil/${encodeURIComponent(item.gambar_mobil)}` :
                                '';
                            const gambarMobil = gambarPath ?
                                `<img src="${gambarPath}" alt="Gambar Mobil" width="200" height="200">` :
                                'Tidak ada gambar';

                            tableData.push([
                                i + 1
                                , item.nama_mobil
                                , item.nopol
                                , gambarMobil
                                , `
                                   <button class="btn btn-sm btn-warning">Edit</button>
                                   <button class="btn btn-sm btn-danger">Hapus</button>`
                            ]);
                        });

                        //table.clear().rows.add(tableData).draw();
                        const currentPage = table.page(); // simpan halaman saat ini
                        table.clear().rows.add(tableData).draw(false); // false supaya tetap di halaman sekarang
                        table.page(currentPage).draw(false); // kembali ke halaman sebelumnya

                        $('#loading').hide(); // Sembunyikan spinner setelah data selesai dimuat

                        if (isFirstLoad) {
                            isFirstLoad = false; // Mengatur flag agar spinner tidak muncul di pemuatan berikutnya
                        }
                    }
                    , error: function() {
                        $('#loading').hide(); // Sembunyikan spinner jika terjadi error
                    }
                });
            }

            fetchData(); // Ambil data pertama kali saat halaman dimuat
            setInterval(fetchData, 5000); // Ambil data setiap 5 detik tanpa spinner
        });

    </script>
</body>
</html>
