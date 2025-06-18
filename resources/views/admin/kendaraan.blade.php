<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Kendaraan</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
            <h2 class="mb-0">List Kendaraan</h2>

            <div class="d-flex flex-column align-items-end">
                <span style="white-space: nowrap;">
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </span>

                <div class="d-flex mt-2">
                    <button class="btn btn-primary btn-sm me-3" id="btnTambah" data-bs-toggle="modal" data-bs-target="#tambahKendaraanModal">
                        Tambah Kendaraan
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

    <!-- Modal Tambah -->
    <div class="modal fade" id="tambahKendaraanModal" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="form-tambah-kendaraan" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahLabel">Tambah Kendaraan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nama Mobil</label>
                            <input type="text" class="form-control" name="nama_mobil" required>
                        </div>
                        <div class="mb-3">
                            <label>No Polisi</label>
                            <input type="text" class="form-control" name="nopol" required>
                        </div>
                        <div class="mb-3">
                            <label for="gambar_mobil" class="form-label">Gambar Mobil</label>
                            <input type="file" class="form-control" id="gambar_mobil" name="gambar_mobil" accept="image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @foreach($kendaraans as $kendaraan)
    <div class="modal fade" id="editKendaraanModal-{{ $kendaraan->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="form-edit-kendaraan-{{ $kendaraan->id }}" enctype="multipart/form-data">
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editKendaraanId-{{ $kendaraan->id }}" value="{{ $kendaraan->id }}">
                        <div class="mb-3">
                            <label for="editNamaMobil-{{ $kendaraan->id }}">Nama Mobil</label>
                            <input type="text" class="form-control" name="nama_mobil" id="editNamaMobil-{{ $kendaraan->id }}" value="{{ $kendaraan->nama_mobil }}">
                        </div>
                        <div class="mb-3">
                            <label for="editNopol-{{ $kendaraan->id }}">Nopol</label>
                            <input type="text" class="form-control" name="nopol" id="editNopol-{{ $kendaraan->id }}" value="{{ $kendaraan->nopol }}">
                        </div>
                        <div class="mb-3">
                            <label for="editGambarMobil-{{ $kendaraan->id }}">Username</label>
                            <input type="file" class="form-control" name="gambar_mobil" id="editGambarMobil-{{ $kendaraan->id }}" value="{{ $kendaraan->gambar_mobil }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach


    {{-- modal hapus --}}
    <div class="modal fade" id="hapusKendaraanModal" tabindex="-1" aria-labelledby="hapusKendaraanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="hapusKendaraanModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p>Yakin ingin menghapus kendaraan <strong id="hapusNamaMobil"></strong> (<strong id="hapusNopol"></strong>)?</p>
                    <input type="hidden" id="hapusKendaraanId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="konfirmasiHapusBtnKendaraan">Hapus</button>
                </div>
            </div>
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
                            const gambarMobil = item.gambar_url ?
                                `<img src="${item.gambar_url}" alt="Gambar Mobil" width="200" height="200">` :
                                'Tidak ada gambar';

                            tableData.push([
                                i + 1
                                , item.nama_mobil
                                , item.nopol
                                , gambarMobil
                                , `
                                   <button class="btn btn-sm btn-warning btn-edit-user" data-id="${item.id}" data-nama-mobil="${item.nama_mobil}" data-nopol="${item.nopol}" data-gambar-mobil="${item.gambar_mobil}" data-bs-toggle="modal" data-bs-target="#editKendaraanModal-${item.id}">
                                       Edit
                                   </button>

                                   <button class="btn btn-sm btn-danger btn-hapus-kendaraan" 
                                   data-id="${item.id}" data-nama-mobil="${item.nama_mobil}" data-nopol="${item.nopol}" data-bs-toggle="modal" data-bs-target="#hapusKendaraanModal">Hapus</button>`
                            ]);
                        });

                        //table.clear().rows.add(tableData).draw();
                        const currentPage = table.page(); // simpan halaman saat ini
                        table.clear().rows.add(tableData).draw(false); // false supaya tetap di halaman sekarang
                        table.page(currentPage).draw(false); // kembali ke halaman sebelumnya

                        $('#loading').hide();

                        if (isFirstLoad) {
                            isFirstLoad = false;
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

    <script>
        // tambah
        $('#form-tambah-kendaraan').submit(function(e) {
            e.preventDefault();

            let form = $(this)[0];
            let formData = new FormData(form);

            $.ajax({
                url: '{{ route("tambah.kendaraan") }}'
                , method: 'POST'
                , data: formData
                , processData: false, // wajib untuk FormData
                contentType: false, // wajib untuk FormData
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(res) {
                    alert(res.message);
                    console.log(res.data)
                    $('#tambahKendaraanModal').modal('hide');
                    form[0].reset();
                    form.find('.is-invalid').removeClass('is-invalid'); // hapus error style
                    form.find('.invalid-feedback').remove();
                }
                , error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let message = '';
                        for (let key in errors) {
                            message += errors[key][0] + '\n';
                        }
                        alert(message);
                    } else {
                        alert('Terjadi kesalahan server.');
                        console.log(xhr.responseText); // Boleh aktifkan untuk lihat error detail
                    }
                }
            });
        });


        // edit
        $(document).on('submit', '[id^="form-edit-kendaraan-"]', function(e) {
            e.preventDefault();

            const form = $(this)[0];
            const formData = new FormData(form);
            const id = formData.get('id');

            $.ajax({
                url: `/kendaraan/${id}/edit`
                , method: 'POST'
                , data: formData
                , processData: false
                , contentType: false
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(res) {
                    alert(res.message);
                    $('#editKendaraanModal-' + id).modal('hide');
                    location.reload();
                }
                , error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let message = '';
                        for (let key in errors) {
                            message += errors[key][0] + '\n';
                        }
                        alert(message);
                    } else {
                        alert('Terjadi kesalahan server.');
                        console.log(xhr.responseText);
                    }
                }
            });
        });

        // hapus
        let kendaraanIdToDelete = null;
        $(document).on('click', '.btn-hapus-kendaraan', function() {
            let kendaraanIdToDelete = $(this).data('id');
            let nama_mobil = $(this).data('nama-mobil');
            let nopol = $(this).data('nopol');

            $('#hapusKendaraanId').val(kendaraanIdToDelete);
            $('#hapusNamaMobil').text(nama_mobil);
            $('#hapusNopol').text(nopol);
        });

        $('#konfirmasiHapusBtnKendaraan').click(function() {
            let kendaraanIdToDelete = $('#hapusKendaraanId').val();
            if (!kendaraanIdToDelete) {
                console.log('ID kendaraan kosong!');
                return;
            }

            $.ajax({
                url: '/kendaraan/' + kendaraanIdToDelete + '/hapus'
                , type: 'POST'
                , data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                    , _method: 'DELETE'
                }
                , success: function(res) {
                    alert(res.message);
                    $('#hapusKendaraanModal').modal('hide');
                }
                , error: function(xhr) {
                    alert('Gagal menghapus data. Silakan coba lagi.');
                    console.log(xhr.responseText);
                }
            });
        });

    </script>

</body>
</html>
