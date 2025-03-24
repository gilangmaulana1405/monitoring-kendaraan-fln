<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <meta http-equiv="refresh" content="10"> --}}
    <title>Monitoring Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @livewireStyles
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex flex-column align-items-center">
            <img src="img/fln-logo.png" class="mb-4" width="120px" style="margin-top: -20px;" alt="">
            <h2 class="mb-4">Monitoring Kendaraan</h2>
        </div>

        @livewire('monitoring-kendaraan')
    </div>

    @livewireScripts

</body>
</html>
