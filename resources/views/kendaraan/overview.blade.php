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

        <div class="row">
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @foreach($kendaraan as $k)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="{{ asset($k->image_path) }}" class="card-img-top w-100" style="height: 350px; object-fit: cover;" alt="...">

                    <div class="card-body position-relative">
                        <h5 class="card-title">{{ $k->nama_mobil }}</h5>
                        <p class="card-text">{{ $k->nopol }}</p>
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal{{ $k->id }}">
                            Update Status
                        </button>

                        @if($k->status == 'Stand By')
                        <span class="badge bg-success position-absolute bottom-3 end-0 m-2">Stand By</span>
                        @else
                        <span class="badge bg-warning position-absolute bottom-3 end-0 m-2">Pergi</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Modal Form -->
            <div class="modal fade" id="exampleModal{{ $k->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $k->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel{{ $k->id }}">Update Status Kendaraan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="updateForm{{ $k->id }}" action="{{ url('/kendaraan/update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="id" value="{{ $k->id }}">
                                <div class="mb-3">
                                    <label for="nama_mobil" class="form-label">Nama Mobil</label>
                                    <input type="text" class="form-control" id="nama_mobil" value="{{ $k->nama_mobil }}" style="background-color: #e9ecef; color: #6c757d; cursor: not-allowed;" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="nopol" class="form-label">No Polisi</label>
                                    <input type="text" class="form-control" id="nopol" value="{{ $k->nopol }}" style="background-color: #e9ecef; color: #6c757d; cursor: not-allowed;" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" class="form-select" onchange="toggleInput(this, 'inputText{{ $k->id }}')">
                                        <option value="Stand By" {{ $k->status == 'Stand By' ? 'selected' : '' }}>Stand By</option>
                                        <option value="Pergi" {{ $k->status == 'Pergi' ? 'selected' : '' }}>Pergi</option>
                                    </select>
                                </div>

                                <div class="mb-3" id="inputText{{ $k->id }}" style="display:  {{ $k->status == 'Pergi' ? 'block' : 'none' }};">
                                    <label for="nama_pemakai" class="form-label">Nama Pemakai</label>
                                    <input type="text" class="form-control" name="nama_pemakai">

                                    <label for="departemen" class="form-label">Departemen</label>
                                    <input type="text" class="form-control" name="departemen">

                                    <label for="tujuan" class="form-label">Tujuan</label>
                                    <textarea name="tujuan" class="form-control" placeholder="Masukkan tujuan"></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">Update</button>
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
        // Fungsi untuk menampilkan atau menyembunyikan input tujuan berdasarkan status yang dipilih
        function toggleInput(select, divId) {
            let inputDiv = document.getElementById(divId);
            if (!inputDiv) return;

            let inputs = inputDiv.querySelectorAll("input, textarea");

            if (select.value === "Pergi") {
                inputDiv.style.display = "block";
                inputs.forEach(input => input.setAttribute("required", "required"));
            } else {
                inputDiv.style.display = "none";
                inputs.forEach(input => input.removeAttribute("required")); // Hapus required saat disembunyikan
            }
        }



        document.addEventListener("DOMContentLoaded", function() {
            // Pastikan input tujuan tampil sesuai status awal
            document.querySelectorAll("select[name='status']").forEach(select => {
                let divId = select.getAttribute("onchange").match(/'([^']+)'/)[1]; // Ambil divId dari atribut onchange
                toggleInput(select, divId);
            });

            // Tambahkan event listener untuk submit form menggunakan AJAX
            document.querySelectorAll("form[id^='updateForm']").forEach(form => {
                form.addEventListener("submit", function(e) {
                    e.preventDefault();

                    let formData = new FormData(this);
                    let actionUrl = this.getAttribute("action");

                    fetch(actionUrl, {
                            method: "POST"
                            , body: formData
                            , headers: {
                                "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").getAttribute("content")
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error("Gagal memperbarui data!");
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {

                                // jika sukses
                                let alertBox = document.createElement("div");
                                alertBox.className = "alert alert-success alert-dismissible fade show";
                                alertBox.style.maxWidth = "400px";
                                alertBox.style.margin = "10px auto";
                                alertBox.innerHTML = `
                                ${data.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                `;
                                document.body.prepend(alertBox);

                                // Tutup modal setelah submit
                                let modalElement = document.getElementById("exampleModal" + formData.get("id"));
                                if (modalElement) {
                                    let modalInstance = bootstrap.Modal.getInstance(modalElement);
                                    if (modalInstance) modalInstance.hide();
                                }

                                // Tunggu 1 detik sebelum reload agar alert terlihat
                                setTimeout(() => {
                                    location.reload();
                                }, 5000);
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            alert("Terjadi kesalahan saat memperbarui data.");
                        });
                });
            });
        });

    </script>



</body>
</html>
