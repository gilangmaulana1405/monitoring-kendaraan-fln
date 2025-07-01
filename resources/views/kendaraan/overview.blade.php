<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PT FLN | Kendaraan Operasional</title>

    {{-- offline --}}
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <script src="{{ asset('assets/jquery/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/pusher-8.4.0.min.js') }}"></script>
    <script src="{{ asset('js/echo-1.11.1.js') }}"></script>

</head>

<body class="d-flex" style="min-height: 100vh;">

    <!-- Sidebar -->
    @include('components.sidebar')

    <!-- Main Content -->
    <div class="flex-grow-1 p-4 bg-light">
        <div class="container mt-3">
            <div class="row align-items-center">
                <div class="col-12 col-md-4 text-start mt-3 mt-md-0">
                    <h2 class="mt-0">Kendaraan Operasional</h2>
                </div>

                <div class="col-12 col-md-4 text-center">
                    <img src="img/fln-logo.png" width="120px" alt="" class="my-2">
                </div>

                <div class="col-12 col-md-4 text-end">
                    @auth
                    <div class="text-muted small mt-2">
                        Selamat datang, <strong>{{ auth()->user()->username }}</strong>
                    </div>

                    {{-- Tampilkan hari dan tanggal di atas tombol --}}
                    <div class="text-muted small">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </div>

                    @if(in_array(auth()->user()->jabatan, ['Admin GA', 'Staff GA']))
                    <button type="button"" class=" btn btn-sm btn-primary mt-1" data-bs-toggle="modal" data-bs-target="#tambahKendaraanModal">+ Tambah Kendaraan</button>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger mt-1">Logout</button>
                    </form>
                    @endauth

                    @guest
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary mt-1">Login</a>

                    {{-- Tanggal juga bisa ditampilkan untuk guest jika perlu --}}
                    <div class="text-muted small mt-2">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </div>
                    @endguest
                </div>
            </div>

            {{-- alert sukses untuk crud kendaraan --}}
            @if (session('success'))
            <div id="alertBox" class="alert alert-success alert-dismissible fade show mt-4" role="alert">
                {!! session('success') !!}
            </div>
            @endif

            {{-- Alert error dari validasi --}}
            @if ($errors->any())
            <div id="alertBox" class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{!! $error !!}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- alert untuk input in/out --}}
            <div id="alertBox"></div>

            <div class="row mt-3">
                @foreach($kendaraan as $k)
                <div class="col-md-4 mb-4 kendaraan-card" data-id="{{ $k->id }}">
                    <div class="card">
                        <img src="{{ asset($k->image_path) }}" class="card-img-top w-100" style="height: 350px; object-fit: cover;">
                        <div class="card-body position-relative">
                            <h5 class="card-title">{{ $k->nama_mobil }}</h5>
                            <p class="card-text">{{ $k->nopol }}</p>

                            @auth
                            @if(in_array(auth()->user()->jabatan, ['Admin GA', 'Staff GA', 'Security']))
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal{{ $k->id }}">
                                Update Status
                            </button>
                            @endif
                            @endauth

                            {{-- status dalam card --}}
                            @php
                            $statusClass = match ($k->status) {
                            'Stand By' => 'success',
                            'Pergi' => 'warning',
                            'Perbaikan' => 'danger',
                            default => throw new \Exception('Status tidak dikenal: ' . $k->status),
                            };
                            @endphp

                            <div class="position-absolute bottom-0 end-0 text-end m-2">
                                <span class="badge bg-{{ $statusClass }} mb-1 status-badge">
                                    {{ $k->status }}
                                </span>
                                <br>
                                {{-- time diffforhuman --}}
                                <small class="text-muted waktu-update mt-1 d-block" data-updated="{{ $k->updated_at }}">
                                    {{ $k->updated_at ? $k->updated_at->diffForHumans() : 'Belum pernah diperbarui' }}
                                </small>

                                @auth
                                @if(in_array(auth()->user()->jabatan, ['Admin GA', 'Staff GA']))
                                <div class="mt-2">
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editKendaraanModal-{{ $k->id }}">
                                        Edit
                                    </button>

                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#hapusKendaraanModal{{ $k->id }}">
                                        Hapus
                                    </button>

                                </div>
                                @endif
                                @endauth

                            </div>
                        </div>
                    </div>
                </div>

                {{-- update status kendaraan --}}
                <div class="modal fade" id="modal{{ $k->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Update Status Kendaraan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-danger d-none error-message-box"></div>
                                <form class="updateForm" data-id="{{ $k->id }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id" value="{{ $k->id }}">

                                    <label class="form-label">Kendaraan</label>
                                    <input type="text" class="form-control" name="nama_mobil" value="{{ $k->nama_mobil }}" readonly style="background-color: #e9ecef; pointer-events: none;">
                                    <input type="text" class="form-control mt-2" name="nopol" value="{{ $k->nopol }}" readonly style="background-color: #e9ecef; pointer-events: none;">

                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select statusSelect" data-id="{{ $k->id }}">
                                        <option value="Stand By" {{ $k->status == 'Stand By' ? 'selected' : '' }}>Stand By
                                        </option>
                                        <option value="Pergi" {{ $k->status == 'Pergi' ? 'selected' : '' }}>Pergi</option>
                                        <option value="Perbaikan" {{ $k->status == 'Perbaikan' ? 'selected' : '' }}>Perbaikan
                                        </option>
                                    </select>

                                    <div class="additional-fields mt-3" id="additionalFields{{ $k->id }}" style="display: none;">
                                        <label class="form-label">Nama Pemakai *</label>
                                        <input type="text" class="form-control" name="nama_pemakai">

                                        <label class="form-label">Departemen *</label>
                                        <select name="departemen" class="form-select">
                                            <option value="ENGINEERING">ENGINEERING</option>
                                            <option value="FA">FA</option>
                                            <option value="HR/GA">HR/GA</option>
                                            <option value="HSE">HSE</option>
                                            <option value="IT">IT</option>
                                            <option value="MR">MR</option>
                                            <option value="MAINTENANCE">MAINTENANCE</option>
                                            <option value="MARKETING">MARKETING</option>
                                            <option value="PPIC/RM">PPIC/RM</option>
                                            <option value="PRODUKSI">PRODUKSI</option>
                                            <option value="PURCHASING">PURCHASING</option>
                                            <option value="QUALITY">QUALITY</option>
                                        </select>

                                        <label class="form-label">Driver</label>
                                        <select name="driver" class="form-select driverSelect" data-id="{{ $k->id }}">
                                            <option value="Abas">Abas</option>
                                            <option value="Rahmat">Rahmat</option>
                                            <option value="Fiki">Fiki</option>
                                            <option value="Dwi">Dwi</option>
                                            <option value="Zaenudin">Zaenudin</option>
                                            <option value="Lain-lain">Lain-lain</option>
                                        </select>
                                        <input type="text" class="form-control mt-2 driverLainInput" name="driver_lain" placeholder="Masukkan nama driver lain" style="display:none;">

                                        <label class="form-label">Tujuan *</label>
                                        <input type="text" class="form-control" name="tujuan">

                                        <label class="form-label">Keterangan (opsional)</label>
                                        <textarea name="keterangan" class="form-control"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-3">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- modal tambah --}}
                <div class="modal fade" id="tambahKendaraanModal" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('tambah.kendaraan') }}" method="POST" enctype="multipart/form-data">
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
                                        <input type="file" class="form-control" name="gambar_mobil" id="gambarMobilTambah" onchange="typeof previewGambar === 'function' && previewGambar(event)" accept="image/*" required />
                                        <img id="previewGambar" class="img-fluid mt-2" style="max-height: 200px; width: 200px; display: none;">
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

                {{-- modal hapus --}}
                <div class="modal fade" id="hapusKendaraanModal{{ $k->id }}" tabindex="-1" aria-labelledby="hapusModalLabel{{ $k->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('hapus.kendaraan', $k->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="hapusModalLabel{{ $k->id }}">Konfirmasi Hapus</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <p>Yakin ingin menghapus kendaraan berikut?</p>
                                            <ul>
                                                <li>Nama Mobil : <strong>{{ $k->nama_mobil }}</strong></li>
                                                <li>No Polisi : <strong>{{ $k->nopol }}</strong></li>
                                            </ul>
                                        </div>

                                        <div class="col-md-7 text-end">
                                            <img src="{{ asset('/storage/mobil/' . $k->gambar_mobil) }}" alt="Gambar Mobil" class="img-fluid" style="height:250px; width:500px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- modal edit --}}
                <div class="modal fade" id="editKendaraanModal-{{ $k->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                       <form action="{{ route('edit.kendaraan', $k->id) }}" method="POST" enctype="multipart/form-data">
                           @csrf
                           @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Kendaraan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" id="editKendaraanId-{{ $k->id }}" value="{{ $k->id }}">
                                    <div class="mb-3">
                                        <label for="editNamaMobil-{{ $k->id }}">Nama Mobil</label>
                                        <input type="text" class="form-control" name="nama_mobil" id="editNamaMobil-{{ $k->id }}" value="{{ $k->nama_mobil }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="editNopol-{{ $k->id }}">Nopol</label>
                                        <input type="text" class="form-control" name="nopol" id="editNopol-{{ $k->id }}" value="{{ $k->nopol }}">
                                    </div>

                                    <div class="mb-3">
                                        <label for="editGambarMobil-{{ $k->id }}">Gambar Mobil Saat Ini</label><br>
                                        <img src="{{ asset('storage/mobil/' . $k->gambar_mobil) }}" alt="Gambar Mobil" class="img-fluid mb-2" style="max-height: 200px; width: 200px;">
                                    </div>

                                    <div class="mb-3">
                                        <label for="editGambarMobil-{{ $k->id }}">Ganti Gambar</label>
                                        <input type="file" class="form-control" name="gambar_mobil" id="editGambarMobil-{{ $k->id }}" onchange="previewGambar(event, '{{ $k->id }}')">
                                        <img id="previewGambar-{{ $k->id }}" class="img-fluid mt-2" style="max-height: 200px; width: 200px;" />
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Edit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @endforeach
            </div>
        </div>
    </div>



    {{-- offline --}}
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/vendor/dayjs/dayjs.min.js') }}"></script>
    <script src="{{ asset('js/vendor/dayjs/plugin/relativeTime.js') }}"></script>
    <script src="{{ asset('js/vendor/dayjs/locale/id.js') }}"></script>


    <script>
        // preview gambar
        function previewGambar(event, id = null) {
            const input = event.target;
            const preview = id ?
                document.getElementById('previewGambar-' + id) :
                document.getElementById('previewGambar');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        $('#gambarMobilTambah').on('change', function(event) {
            previewGambar(event);
        });

        // alert
        setTimeout(() => {
            const alert = document.getElementById('alertBox');
            if (alert) {
                // Tambahkan class fade-out dan hapus setelah animasi
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500); // Tunggu animasi fade selesai
            }
        }, 3000);

    </script>

    {{-- input in/out --}}
    <script>
        // updated_at dinamis berubah ubah
        dayjs.locale('id-custom', {
            name: 'id-custom'
            , relativeTime: {
                future: 'dalam %s'
                , past: '%s yang lalu'
                , s: 'baru saja diubah'
                , m: '1 menit'
                , mm: '%d menit'
                , h: '1 jam'
                , hh: '%d jam'
                , d: '1 hari'
                , dd: '%d hari'
                , M: '1 bulan'
                , MM: '%d bulan'
                , y: '1 tahun'
                , yy: '%d tahun'
            }
        });

        dayjs.extend(dayjs_plugin_relativeTime);
        dayjs.locale('id-custom');

        window.kendaraanIds = @json($kendaraanIds);

        document.addEventListener("DOMContentLoaded", function() {

            // Inisialisasi Echo dan Pusher
            window.Echo = new Echo({
                broadcaster: 'pusher'
                , key: '{{ config("reverb.apps.apps.0.key") }}'
                , cluster: '{{ env("REVERB_APP_CLUSTER", "mt1") }}', // cluster diperlukan saat broadcaster 'pusher'
                wsHost: window.location.hostname
                , wsPort: 6001
                , forceTLS: false
                , disableStats: true
                , enabledTransports: ['ws']
            , });

            // Cek koneksi WebSocket
            window.Echo.connector.pusher.connection.bind('connected', () => {
                console.log('WebSocket Connected');
            });

            // Pastikan kendaraanIds sudah didefinisikan
            if (Array.isArray(window.kendaraanIds)) {
                window.kendaraanIds.forEach(id => {
                    window.Echo.channel(`kendaraan.${id}`)
                        .listen('.KendaraanUpdated', (event) => {
                            const card = document.querySelector(`[data-id='${event.id}']`);
                            if (!card) return;

                            const badge = card.querySelector(".status-badge");
                            badge.textContent = event.status;
                            badge.classList.remove("bg-success", "bg-warning", "bg-danger");

                            switch (event.status) {
                                case "Stand By":
                                    badge.classList.add("bg-success");
                                    break;
                                case "Pergi":
                                    badge.classList.add("bg-warning");
                                    break;
                                case "Perbaikan":
                                    badge.classList.add("bg-danger");
                                    break;
                            }

                            const waktu = card.querySelector(".waktu-update");
                            waktu.textContent = dayjs(event.updated_at).fromNow();
                            waktu.setAttribute("data-updated", event.updated_at);
                        });
                });
            } else {
                console.warn('kendaraanIds belum didefinisikan atau bukan array.');
            }

            // update_at berubah ubah dinamis
            setInterval(() => {
                document.querySelectorAll(".waktu-update").forEach(el => {
                    const updatedAt = el.getAttribute("data-updated");
                    if (updatedAt) {
                        const diffInSeconds = dayjs().diff(dayjs(updatedAt), 'second');
                        if (diffInSeconds < 60) {
                            el.textContent = "Baru saja diubah";
                        } else {
                            el.textContent = dayjs(updatedAt).fromNow();
                        }
                    } else {
                        el.textContent = "Belum pernah diperbarui";
                    }
                });
            }, 1000);

            //ketika pilih pergi maka muncul form
            document.querySelectorAll(".statusSelect").forEach(select => {
                toggleAdditionalFields(select);
                select.addEventListener("change", function() {
                    toggleAdditionalFields(this);
                });
            });

            function toggleAdditionalFields(select) {
                let div = document.getElementById("additionalFields" + select.dataset.id);
                const inputs = div.querySelectorAll("input, textarea, select");

                if (select.value === "Pergi") {
                    div.style.display = "block";
                } else {
                    div.style.display = "none";
                }
            }

            // Tampilkan input 'driver lain' jika pilihannya 'Lain-lain'
            document.querySelectorAll(".driverSelect").forEach(select => {
                toggleDriverInput(select);
                select.addEventListener("change", function() {
                    toggleDriverInput(this);
                });
            });

            function toggleDriverInput(select) {
                const id = select.dataset.id;
                const form = select.closest("form");
                const inputLain = form.querySelector("input[name='driver_lain']");

                if (select.value === "Lain-lain") {
                    inputLain.style.display = "block";
                } else {
                    inputLain.style.display = "none";
                    inputLain.value = ""; // kosongkan kalau tidak dipakai
                }
            }

            // form submit
            document.querySelectorAll(".updateForm").forEach(form => {
                form.addEventListener("submit", function(e) {
                    e.preventDefault();

                    let status = form.querySelector("select[name='status']").value;

                    if (status === "Pergi") {
                        let mobil = form.querySelector("input[name='nama_mobil']").value.trim();
                        let nopol = form.querySelector("input[name='nopol']").value.trim();
                        let nama = form.querySelector("input[name='nama_pemakai']").value.trim();
                        let departemen = form.querySelector("select[name='departemen']").value.trim();

                        let driverSelect = form.querySelector("select[name='driver']").value.trim();
                        let driver = driverSelect === "Lain-lain" ?
                            form.querySelector("input[name='driver_lain']").value.trim() :
                            driverSelect;

                        let tujuan = form.querySelector("input[name='tujuan']").value.trim();

                        if (!nama || !departemen || !driver || !tujuan) {
                            alert("Data masih ada yang kosong dan harus diisi!");
                            return;
                        }
                    }

                    // Proses fetch
                    let formData = new FormData(form);
                    let id = form.dataset.id;

                    fetch("/kendaraan/update", {
                            method: "POST"
                            , body: formData
                            , headers: {
                                "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").getAttribute("content")
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            // console.log(data);
                            if (data.success) {
                                document.getElementById("alertBox").innerHTML = `<div class='alert alert-success'>${data.message}</div>`;
                                setTimeout(() => {
                                    document.getElementById("alertBox").innerHTML = "";
                                }, 3000);


                                // Update badge status (local update)
                                let card = document.querySelector(`[data-id='${id}']`);
                                const badge = card.querySelector(".status-badge");
                                badge.textContent = data.status;
                                badge.classList.remove("bg-success", "bg-warning", "bg-danger");

                                switch (data.status) {
                                    case "Stand By":
                                        badge.classList.add("bg-success");
                                        break;
                                    case "Pergi":
                                        badge.classList.add("bg-warning");
                                        break;
                                    case "Perbaikan":
                                        badge.classList.add("bg-danger");
                                        break;
                                }

                                let modal = bootstrap.Modal.getInstance(document.getElementById('modal' + id));
                                modal.hide();

                            } else {
                                document.getElementById("alertBox").innerHTML = `<div class='alert alert-danger'>Terjadi kesalahan, silahkan coba lagi.</div>`;
                                setTimeout(() => {
                                    document.getElementById("alertBox").innerHTML = "";
                                }, 3000);
                            }
                        })
                        .catch(error => console.error("Error:", error));
                });
            });
        });

    </script>

</body>

</html>
