<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Events\KendaraanUpdated;
use App\Models\HistoryKendaraan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

class KendaraanController extends Controller
{

    // menampilkan gambar
    private function getImagePath($nopol)
    {
        $imgPath = public_path('img/mobil/');
        $extensions = ['jpeg', 'jpg', 'png', 'webp'];
        $image = '';

        foreach ($extensions as $ext) {
            if (File::exists($imgPath . $nopol . '.' . $ext)) {
                $image = 'img/mobil/' . $nopol . '.' . $ext;
                break;
            }
        }

        return $image;
    }

    // sort by status
    private function sortByStatus($kendaraan)
    {
        return $kendaraan->sortBy(function ($item) {
            return match ($item->status) {
                'Stand By'  => 1,
                'Pergi'     => 2,
                'Perbaikan' => 3,
                default     => 4,
            };
        })->values();
    }
    // monitoring kendaraan di halaman user
    public function index()
    {
        // Ambil semua data kendaraan
        $kendaraan = Kendaraan::all()->map(function ($k) {
            // Ambil gambar kendaraan
            $k->image_path = $this->getImagePath($k->nopol);

            // Ambil history terakhir untuk kendaraan ini
            $lastHistory = HistoryKendaraan::where('kendaraan_id', $k->id)
                ->latest()
                ->first();  // Dapatkan history terbaru

            // Tambahkan data dari history jika ada
            $k->nama_pemakai = $lastHistory ? $lastHistory->nama_pemakai : '-';
            $k->departemen = $lastHistory ? $lastHistory->departemen : '-';
            $k->driver = $lastHistory ? $lastHistory->driver : '-';
            $k->tujuan = $lastHistory ? $lastHistory->tujuan : '-';
            $k->keterangan = $lastHistory ? $lastHistory->keterangan : '-';

            return $k;
        });

        // Urutkan berdasarkan status
        $kendaraan = $this->sortByStatus($kendaraan);

        // Kirim data ke view
        return view('kendaraan.index', compact('kendaraan'));
    }

    public function getData()
    {
        // Urutkan kendaraan berdasarkan status dengan urutan yang diinginkan
        $kendaraan = Kendaraan::orderByRaw("FIELD(status, 'Stand By', 'Pergi', 'Perbaikan')")
            ->latest()  // Bisa diatur jika ingin urutkan berdasarkan waktu update
            ->take(8)   // Ambil hanya 8 kendaraan
            ->get();

        // Gabungkan dengan history terakhir untuk setiap kendaraan
        $kendaraan = $kendaraan->map(function ($k) {
            $image = $this->getImagePath($k->nopol);

            // Ambil history terakhir (status terbaru) untuk kendaraan ini
            $lastHistory = HistoryKendaraan::where('kendaraan_id', $k->id)
                ->latest()
                ->first();  // Dapatkan history terbaru

            return [
                'nama_mobil'    => $k->nama_mobil,
                'image_path'    => asset($image),
                'nopol'         => $k->nopol,
                'status'        => $k->status,
                'nama_pemakai'  => $lastHistory ? $lastHistory->nama_pemakai : '-',
                'departemen'    => $lastHistory ? $lastHistory->departemen : '-',
                'driver'        => $lastHistory ? $lastHistory->driver : '-',
                'tujuan'        => $lastHistory ? $lastHistory->tujuan : '-',
                'keterangan'    => $lastHistory ? $lastHistory->keterangan : '-',
                'updated_at'    => Carbon::parse($k->updated_at)
                    ->timezone('Asia/Jakarta')
                    ->toIso8601String(),
            ];
        });

        return response()->json($kendaraan);
    }



    // List kendaraan untuk update data
    public function kendaraan()
    {
        $kendaraan = Kendaraan::all()->map(function ($k) {
            $k->image_path = $this->getImagePath($k->nopol);
            return $k;
        });

        $kendaraan = $this->sortByStatus($kendaraan);

        // Ambil ID kendaraan yang akan digunakan di frontend
        $kendaraanIds = $kendaraan->pluck('id');

        return view('kendaraan.overview', compact('kendaraan', 'kendaraanIds'));
    }
    public function update(Request $request)
    {
        $rules = [
            'status' => 'required|string',
        ];

        if ($request->status === 'Pergi') {
            $rules['nama_pemakai'] = 'nullable|string';
            $rules['departemen'] = 'nullable|string';
            $rules['driver'] = 'required|string';
            if ($request->driver === 'Lain-lain') {
                $rules['driver_lain'] = 'required|string';
            }
            $rules['tujuan'] = 'nullable|string';
            $rules['keterangan'] = 'nullable|string';
        }

        $request->validate($rules);

        $kendaraan = Kendaraan::findOrFail($request->id);

        $lastPergi = HistoryKendaraan::where('kendaraan_id', $kendaraan->id)
            ->where('status', 'Pergi')
            ->latest()
            ->first();

        if ($request->status === 'Pergi') {
            $namaPemakai = $request->nama_pemakai ?? $lastPergi?->nama_pemakai;
            $departemen = $request->departemen ?? $lastPergi?->departemen;
            $driver = $request->driver === 'Lain-lain'
                ? ($request->driver_lain ?? $lastPergi?->driver)
                : ($request->driver ?? $lastPergi?->driver);
            $tujuan = $request->tujuan ?? $lastPergi?->tujuan;
            $keterangan = $request->keterangan ?? $lastPergi?->keterangan;
        } else {
            $namaPemakai = $lastPergi?->nama_pemakai;
            $departemen = $lastPergi?->departemen;
            $driver = $lastPergi?->driver;
            $tujuan = $lastPergi?->tujuan;
            $keterangan = $lastPergi?->keterangan;
        }

        // Capitalize only driver and nama_pemakai
        $namaPemakai = $namaPemakai ? Str::title($namaPemakai) : null;
        $driver = $driver ? Str::title($driver) : null;

        // HANYA update status kendaraan
        $kendaraan->status = $request->status;
        $kendaraan->updated_at = now();
        $kendaraan->save();

        // Simpan snapshot ke history
        HistoryKendaraan::create([
            'kendaraan_id' => $kendaraan->id,
            'nama_mobil' => $kendaraan->nama_mobil,
            'nopol' => $kendaraan->nopol,
            'status' => $request->status,
            'nama_pemakai' => $namaPemakai,
            'departemen' => $departemen,
            'driver' => $driver,
            'tujuan' => $tujuan,
            'keterangan' => $keterangan,
            'pic_update' => auth()->user()->username,
        ]);

        broadcast(new KendaraanUpdated($kendaraan));

        return response()->json([
            'success' => true,
            'message' => "Status kendaraan <strong>{$kendaraan->nama_mobil} {$kendaraan->nopol}</strong> berhasil diperbarui!",
            'status' => $kendaraan->status,
        ]);
    }
}
