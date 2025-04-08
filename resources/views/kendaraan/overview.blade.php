<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Monitoring Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row align-items-center">
            <div class="col-12 col-md-4 d-flex flex-column align-items-center align-items-md-start">
                <h2 class="mb-2">
                    <img src="img/fln-logo.png" width="120px" alt="">
                </h2>
            </div>

            <div class="col-12 col-md-5 ms-md-auto text-center text-md-end mt-3 mt-md-0">
                <h2 class="mt-0">List Kendaraan Operasional</h2>
            </div>
        </div>

        <div id="alertBox"></div>
        <div class="row" id="kendaraanList">
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
                            <form class="updateForm" data-id="{{ $k->id }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="id" value="{{ $k->id }}">

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
                                    <input type="text" class="form-control" name="departemen">

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
                    let formData = new FormData(this);
                    let id = this.dataset.id;
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
                                }, 3000);

                                let card = document.querySelector(`.kendaraan-card[data-id='${id}']`);
                                let status = formData.get("status");
                                let color = {
                                    "Stand By": "success"
                                    , "Pergi": "warning"
                                    , "Perbaikan": "danger"
                                } [status] || "secondary";

                                let badge = card.querySelector(".status-badge");
                                if (badge) {
                                    badge.textContent = status;
                                    badge.className = `badge bg-${color} mb-1 status-badge`;
                                }

                                let waktu = card.querySelector(".waktu-update");
                                if (waktu) {
                                    waktu.textContent = "Baru saja diperbarui";
                                    waktu.classList.add("mt-1", "d-block");
                                }

                                let modalElement = document.getElementById("modal" + id);
                                bootstrap.Modal.getInstance(modalElement).hide();
                            }
                        })
                        .catch(error => console.error("Error:", error));
                });
            });
        });

    </script>

</body>

</html>
