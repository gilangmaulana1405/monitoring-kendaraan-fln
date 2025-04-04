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
                        <span class="badge bg-{{ $k->status == 'Stand By' ? 'success' : 'warning' }} position-absolute bottom-3 end-0 m-2 status-badge">{{ $k->status }}</span>
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
                                    <option value="Stand By" {{ $k->status == 'Stand By' ? 'selected' : '' }}>Stand By</option>
                                    <option value="Pergi" {{ $k->status == 'Pergi' ? 'selected' : '' }}>Pergi</option>
                                </select>
                                <div class="additional-fields mt-3" id="additionalFields{{ $k->id }}" style="display: none;">
                                    <label class="form-label">Nama Pemakai</label>
                                    <input type="text" class="form-control" name="nama_pemakai">
                                    <label class="form-label">Departemen</label>
                                    <input type="text" class="form-control" name="departemen">
                                    <label class="form-label">Tujuan</label>
                                    <textarea name="tujuan" class="form-control"></textarea>
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
            document.querySelectorAll(".statusSelect").forEach(select => {
                toggleAdditionalFields(select);
                select.addEventListener("change", function() {
                    toggleAdditionalFields(this);
                });
            });

            function toggleAdditionalFields(select) {
                let div = document.getElementById("additionalFields" + select.dataset.id);
                div.style.display = select.value === "Pergi" ? "block" : "none";
            }

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
                                card.querySelector(".status-badge").textContent = formData.get("status");
                                card.querySelector(".status-badge").className = `badge bg-${formData.get("status") === 'Stand By' ? 'success' : 'warning'} position-absolute bottom-3 end-0 m-2 status-badge`;
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
