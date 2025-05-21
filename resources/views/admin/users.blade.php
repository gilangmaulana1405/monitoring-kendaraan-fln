<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Users</title>

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
            <h2 class="mb-0">List Users</h2>

            <div class="d-flex flex-column align-items-end">
                <span style="white-space: nowrap;">
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </span>

                <div class="d-flex mt-2">
                    <button class="btn btn-primary btn-sm me-3">
                        Tambah Users
                    </button>
                    
                    <a href="/admin" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <div id="loading" style="text-align: center; margin: 30px 0; display: none;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
        </div>

        @if (session('success'))
        <div id="success-alert" class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if ($errors->any())
        <div id="error-alert" class="alert alert-danger">
            Gagal menyimpan data. Silakan periksa input Anda.
        </div>
        @endif

        <div class="table-responsive">
            <table id="users-table" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pengguna</th>
                        <th>Jabatan</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="gantiPasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('users.gantiPassword') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="gantiPasswordModalLabel">Ganti Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="user_id" id="modalUserId">
                        <input type="text" class="form-control mb-2" id="modalUsername" readonly style="background-color: #e9ecef; pointer-events: none;">
                        <div class="mb-3">
                            <label>Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control" required>
                            @error('current_password') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label>Password Baru</label>
                            <input type="password" name="new_password" class="form-control" required>
                            @error('new_password') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label>Konfirmasi Password Baru</label>
                            <input type="password" name="new_password_confirmation" class="form-control" required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- DataTables non cdn offline JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/js/jquery.dataTables.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            const table = $('#users-table').DataTable({
                columns: [{
                        title: "No"
                        , width: "40px"
                    }
                    , {
                        title: "Nama Pengguna"
                    }
                    , {
                        title: "Jabatan"
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
                    url: "{{ route('list.users.data') }}"
                    , type: 'GET'
                    , dataType: 'json'
                    , success: function(data) {
                        let tableData = [];

                        $.each(data, function(i, item) {
                            tableData.push([
                                i + 1
                                , item.username
                                , item.jabatan
                                , `<button class="btn btn-sm btn-info btn-password" data-bs-toggle="modal" data-bs-target="#gantiPasswordModal" data-id="${item.id}" data-username="${item.username}">Ganti Password</button>
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

            //alert hilang
            setTimeout(function() {
                $('#success-alert, #error-alert').fadeOut('slow');
            }, 3000); // 3 detik

            $(document).on('click', '.btn-password', function() {
                const username = $(this).data('username');
                const userId = $(this).data('id');

                $('#modalUsername').val(username); // tampilkan username di input
                $('#modalUserId').val(userId); // simpan ID user jika dibutuhkan
            });
        });

    </script>
</body>
</html>
