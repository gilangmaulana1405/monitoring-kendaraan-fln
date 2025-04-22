<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">

        <div class="text-center" style="margin-top: -20px;">
            <img src="img/fln-logo.png" width="120px" alt="">
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">History Kendaraan</h2>
            <span style="white-space: nowrap;">
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </span>
        </div>
    </div>

    <div class="table-responsive">
        <table id="history-table" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Update</th> <!-- Pindahkan kolom updated_at ke sini -->
                    <th>Nama Mobil</th>
                    <th>No. Polisi</th>
                    <th>Status</th>
                    <th>Nama Pemakai</th>
                    <th>Departemen</th>
                    <th>Driver</th>
                    <th>Tujuan</th>
                    <th>Keterangan</th>
                    <th>PIC Update</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $.ajax({
                url: "{{ route('history.kendaraan.data') }}"
                , type: 'GET'
                , dataType: 'json'
                , success: function(data) {
                    let tableData = [];
                    $.each(data, function(i, item) {

                        // konversi Tanggal
                        const date = new Date(item.updated_at);
                        const options = {
                            weekday: 'long'
                            , day: '2-digit'
                            , month: 'long'
                            , year: 'numeric'
                            , locale: 'id-ID'
                        };
                        const formattedDate = new Intl.DateTimeFormat('id-ID', options).format(date);


                        tableData.push([
                            i + 1
                            , formattedDate
                            , item.nama_mobil
                            , item.nopol
                            , item.status
                            , item.nama_pemakai
                            , item.departemen
                            , item.driver
                            , item.tujuan
                            , item.keterangan
                            , item.pic_update
                        ]);
                    });

                    $('#history-table').DataTable({
                        data: tableData
                        , columns: [{
                                title: "No"
                            }
                            , {
                                title: "Tanggal Update"
                            }, // Kolom "Tanggal Update"
                            {
                                title: "Nama Mobil"
                            }
                            , {
                                title: "No. Polisi"
                            }
                            , {
                                title: "Status"
                            }
                            , {
                                title: "Nama Pemakai"
                            }
                            , {
                                title: "Departemen"
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
                        , ]
                        , pageLength: 10
                    , });
                }
            });
        });

    </script>

</body>
</html>
