<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>List Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row align-items-center">
            {{-- Judul di kiri --}}
            <div class="col-12 col-md-4 text-start mt-3 mt-md-0">
                <h2 class="mt-0">List Kendaraan Operasional</h2>
            </div>

            {{-- Logo di tengah --}}
            <div class="col-12 col-md-4 text-center">
                <img src="img/fln-logo.png" width="120px" alt="" class="my-2">
            </div>

            {{-- Tombol logout di kanan --}}
            <div class="col-12 col-md-4 text-end">
                <div class="text-muted small mt-2">
                    Selamat datang, <strong>{{ auth()->user()->username }}</strong>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger mt-1">Logout</button>
                </form>
            </div>
        </div>

        <div id="alertBox"></div>
        <div class="row">
            @foreach($kendaraan as $k)
            <div class="col-md-4 mb-4 kendaraan-card" data-id="{{ $k->id }}">
                <div class="card">
                    <img src="{{ asset($k->image_path) }}" class="card-img-top w-100" style="height: 350px; object-fit: cover;">
                    <div class="card-body position-relative">
                        <h5 class="card-title">{{ $k->nama_mobil }}</h5>
                        <p class="card-text">{{ $k->nopol }}</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal{{ $k->id }}">Update Status</button>

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
                            <small class="text-muted waktu-update mt-1 d-block">
                                @php
                                if ($k->updated_at) {
                                $diffInMinutes = $k->updated_at->diffInMinutes(now());
                                $diffInHours = $k->updated_at->diffInHours(now());
                                $diffInDays = $k->updated_at->diffInDays(now());

                                if ($diffInMinutes < 60) { $output=$diffInMinutes . ' menit yang lalu' ; } elseif ($diffInHours < 24) { $output=$diffInHours . ' jam yang lalu' ; } else { $output=$diffInDays . ' hari yang lalu' ; } } else { $output='Belum pernah diperbarui' ; } @endphp {{ $output }} </small>
                        </div>
                    </div>
                </div>
            </div>

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
                                        <option value="MAINTENANCE">MAINTENANCE</option>
                                        <option value="MARKETING">MARKETING</option>
                                        <option value="PPIC">PPIC</option>
                                        <option value="PRODUKSI">PRODUKSI</option>
                                        <option value="PURCHASING">PURCHASING</option>
                                        <option value="QUALITY">QUALITY</option>
                                    </select>

                                    <label class="form-label">Driver</label>
                                    <select name="driver" class="form-select">
                                        <option value="Abas">Abas</option>
                                        <option value="Kosasih">Kosasih</option>
                                        <option value="Rahmat">Rahmat</option>
                                        <option value="Fiki">Fiki</option>
                                    </select>

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
            @endforeach
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

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
            // end

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
                        let driver = form.querySelector("select[name='driver']").value.trim();
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
                            if (data.success) {
                                document.getElementById("alertBox").innerHTML = `<div class='alert alert-success'>${data.message}</div>`;
                                setTimeout(() => {
                                    document.getElementById("alertBox").innerHTML = "";

                                    let card = document.querySelector(`[data-id='${id}']`);
                                    const badge = card.querySelector(".status-badge");
                                    badge.textContent = data.status;

                                    // Hapus semua kelas warna lama
                                    badge.classList.remove("bg-success", "bg-warning", "bg-danger");

                                    // Tambahkan class baru sesuai status
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
                                    location.reload();
                                }, 3000);

                                let modal = bootstrap.Modal.getInstance(document.getElementById('modal' + id));
                                modal.hide();
                            } else {
                                document.getElementById("alertBox").innerHTML = `<div class='alert alert-danger'>Terjadi kesalahan, coba lagi.</div>`;
                            }
                        })
                        .catch(error => console.error("Error:", error));
                });
            });
        });

    </script>

</body>

</html>
