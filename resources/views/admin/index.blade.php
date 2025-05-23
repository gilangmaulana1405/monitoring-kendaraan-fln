<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.bundle.min.css') }}" rel="stylesheet">
</head>
<body class="d-flex" style="min-height: 100vh;">

    <!-- Sidebar -->
    <div class="bg-primary text-white p-3" style="width: 250px; min-height: 100vh;">
        <h4 class="text-center mb-4">FLN GA</h4>

        <a href="#" class="text-white d-block py-2 px-3 text-decoration-none">Dashboard</a>
        <a href="{{ route('list.users') }}" class="text-white d-block py-2 px-3 text-decoration-none">Users</a>

        <!-- Kendaraan Menu -->
        <a class="text-white d-block py-2 px-3 text-decoration-none" data-bs-toggle="collapse" href="#kendaraanMenu" role="button" aria-expanded="false" aria-controls="kendaraanMenu">
            Kendaraan
        </a>
        <div class="collapse" id="kendaraanMenu">
            <a href="{{ route('list.kendaraan') }}" class="text-white d-block py-1 ps-5 text-decoration-none">• List Kendaraan</a>
            <a href="{{ route('inputkeluarmasuk.kendaraan') }}" class="text-white d-block py-1 ps-5 text-decoration-none">• Input Keluar Masuk</a>
            <a href="{{ route('monitoring.kendaraan') }}" class="text-white d-block py-1 ps-5 text-decoration-none">• Monitoring</a>
            <a href="{{ route('history.kendaraan') }}" class="text-white d-block py-1 ps-5 text-decoration-none">• History</a>
        </div>

        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="text-white d-block py-2 px-3 text-decoration-none bg-transparent border-0">
                Logout
            </button>
        </form>
    </div>



    <!-- Main Content -->
    <div class="flex-grow-1 p-4 bg-light">
        <h1>Dashboard</h1>
        <div class="card mt-4">
            <div class="card-body">
                Selamat datang <strong>{{ auth()->user()->username }}</strong>! Ini adalah dashboard utama.
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

</body>
</html>
