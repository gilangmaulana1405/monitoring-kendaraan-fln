<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Kendaraan</title>

    {{-- datatables cdn --}}
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

    {{-- datatables non cdn / offline --}}
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/jquery/jquery-3.6.0.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/datatables/css/jquery.dataTables.min.css') }}">
</head>

<body>
    <div class="container mt-5">
        <div class="text-center" style="margin-top: -20px;">
            <img src="/img/fln-logo.png" width="120px" alt="">
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">History Kendaraan</h2>

            <div class="d-flex flex-column align-items-end">
                <span style="white-space: nowrap;">
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </span>

                <div class="d-flex mt-2">
                    <a href="/admin" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <div id="loading" style="text-align: center; margin: 30px 0; display: none;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
        </div>

        <div class="table-responsive">
            <table id="history-table" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Id History</th>
                        <th>Tanggal Update</th>
                        <th>Jam</th>
                        <th>Mobil</th>
                        <th>Pemakai</th>
                        <th>Driver</th>
                        <th>Tujuan</th>
                        <th>Keterangan</th>
                        <th>PIC Update</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- DataTables CDN JS -->
    {{-- <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> --}}

    <!-- DataTables non cdn offline JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/js/jquery.dataTables.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            const table = $('#history-table').DataTable({
                columns: [{
                        title: "No"
                    }
                    , {
                        title: "Id History"
                    }
                    , {
                        title: "Tanggal Update"
                    }
                    , {
                        title: "Jam Update"
                    }
                    , {
                        title: "Mobil"
                    }
                    , {
                        title: "Pemakai"
                    }
                    , {
                        title: "Driver"
                    }
                    , {
                        title: "Tujuan"
                    }
                    , {
                        title: "Keterangan"
                    }
                    , {
                        title: "PIC Update"
                    }
                ]
                , pageLength: 10
            });

            let isFirstLoad = true; // Mengatur apakah ini pemuatan pertama kali

            function fetchData() {
                if (isFirstLoad) {
                    $('#loading').show();
                }

                $.ajax({
                    url: "{{ route('history.kendaraan.data') }}"
                    , type: 'GET'
                    , dataType: 'json'
                    , success: function(data) {
                        let tableData = [];

                        $.each(data, function(i, item) {
                            const date = new Date(item.updated_at);
                            const tanggalUpdate = new Intl.DateTimeFormat('id-ID', {
                                weekday: 'long'
                                , day: '2-digit'
                                , month: 'long'
                                , year: 'numeric'
                            }).format(date);

                            const jamUpdate = date.toLocaleTimeString('id-ID', {
                                hour: '2-digit'
                                , minute: '2-digit'
                                , hour12: false
                            });


                            tableData.push([
                                i + 1
                                , item.id
                                , tanggalUpdate
                                , jamUpdate
                                , item.mobil ?? '-'
                                , item.nama_pemakai ?? '-'
                                , item.driver ?? '-'
                                , item.tujuan ?? '-'
                                , item.keterangan ?? '-'
                                , item.pic_update ?? '-'
                            ]);

                        });


                        const currentPage = table.page();
                        table.clear().rows.add(tableData).draw(false);
                        table.page(currentPage).draw(false);

                        $('#loading').hide();
                        if (isFirstLoad) isFirstLoad = false;
                    }
                    , error: function() {
                        $('#loading').hide();
                    }
                });
            }

            fetchData(); // Ambil data pertama kali saat halaman dimuat
            setInterval(fetchData, 5000); // Ambil data setiap 5 detik tanpa spinner
        });

    </script>
</body>
</html>
