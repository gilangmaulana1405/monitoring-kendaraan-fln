@extends('layouts.app')

@section('content')
<div>

    @if (session()->has('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif


    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama Mobil</th>
                <th width="100">Gambar</th>
                <th>No Polisi</th>
                <th>Status</th>
                <th>Pemakai</th>
                <th>Tujuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kendaraan as $k)
            <tr>
                <td>{{ $k->nama_mobil }}</td>
                <td>
                    <img src="{{ asset($k->image_path) }}" style="height: 100px; width: 100px;">
                </td>
                <td>{{ $k->nopol }}</td>
                <td>
                    @if($k->status == 'Stand By')
                    <span class="badge bg-success">Stand By</span>
                    @else
                    <span class="badge bg-warning">Pergi</span>
                    Jam {{ \Illuminate\Support\Carbon::parse($k->updated_at)->timezone('Asia/Jakarta')->format('H:i:s') }}
                    @endif
                </td>
                <td>{{ $k->nama_pemakai }} - {{ $k->departemen }} </td>
                <td>{{ $k->tujuan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
