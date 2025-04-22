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

        <div class="table-responsive">
            <table id="history-table" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Update</th>
                        <th>Jam</th>
                        <th>Mobil</th>
                        <th>Status</th>
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

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            // Function to fetch data and update table
            function fetchData() {
                $.ajax({
                    url: "{{ route('history.kendaraan.data') }}", // URL endpoint untuk mengambil data history
                    type: 'GET'
                    , dataType: 'json'
                    , success: function(data) {
                        let tableData = [];
                        $.each(data, function(i, item) {
                            // Konversi Tanggal
                            const date = new Date(item.updated_at);
                            const tanggalUpdate = new Intl.DateTimeFormat('id-ID', {
                                weekday: 'long'
                                , day: '2-digit'
                                , month: 'long'
                                , year: 'numeric'
                            }).format(date);

                            // Format jam
                            const jamUpdate = date.toLocaleTimeString('id-ID', {
                                hour: '2-digit'
                                , minute: '2-digit'
                                , hour12: false
                            });

                            // Warnai badge status
                            let statusBadge = '';
                            switch (item.status.toLowerCase()) {
                                case 'stand by':
                                    statusBadge = '<span class="badge bg-success">Stand By</span>';
                                    break;
                                case 'pergi':
                                    statusBadge = '<span class="badge bg-warning text-dark">Pergi</span>';
                                    break;
                                case 'perbaikan':
                                    statusBadge = '<span class="badge bg-danger">Perbaikan</span>';
                                    break;
                                default:
                                    statusBadge = `<span class="badge bg-secondary">${item.status}</span>`;
                                    break;
                            }

                            tableData.push([
                                i + 1
                                , tanggalUpdate
                                , jamUpdate
                                , item.mobil
                                , statusBadge
                                , item.pemakai
                                , item.driver
                                , item.tujuan
                                , item.keterangan
                                , item.pic_update
                            ]);
                        });

                        // Update DataTable with new data
                        $('#history-table').DataTable().clear().rows.add(tableData).draw();
                    }
                });
            }

            // Initialize DataTable
            $('#history-table').DataTable({
                columns: [{
                        title: "No"
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
                        title: "Status"
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

            // Fetch data every 5 seconds
            setInterval(fetchData, 5000);
        });

    </script>

</body>
</html>
