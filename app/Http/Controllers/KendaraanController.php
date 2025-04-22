<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\HistoryKendaraan;
use Illuminate\Support\Facades\File;

class KendaraanController extends Controller
{

    // menampilkan gambar
    private function getImagePath($nopol)
    {
        $imgPath = public_path('img/');
        $extensions = ['jpeg', 'jpg', 'png', 'webp'];
        $image = 'img/default.png';

        foreach ($extensions as $ext) {
            if (File::exists($imgPath . $nopol . '.' . $ext)) {
                $image = 'img/' . $nopol . '.' . $ext;
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
        $kendaraan = Kendaraan::all()->map(function ($k) {
            $k->image_path = $this->getImagePath($k->nopol);
            return $k;
        });

        $kendaraan = $this->sortByStatus($kendaraan);

        return view('kendaraan.index', compact('kendaraan'));
    }

    public function getData()
    {
        $kendaraan = Kendaraan::all()->map(function ($k) {
            $image = $this->getImagePath($k->nopol);
            return [
                'nama_mobil'    => $k->nama_mobil,
                'image_path'    => asset($image),
                'nopol'         => $k->nopol,
                'status'        => $k->status,
                'nama_pemakai'  => $k->nama_pemakai,
                'departemen'    => $k->departemen,
                'driver'        => $k->driver,
                'tujuan'        => $k->tujuan,
                'keterangan'    => $k->keterangan,
                'updated_at'    => Carbon::parse($k->updated_at)
                    ->timezone('Asia/Jakarta')
                    ->toIso8601String(),
            ];
        });

        $kendaraan = $this->sortByStatus($kendaraan);

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

        return view('kendaraan.overview', compact('kendaraan'));
    }

    public function update(Request $request)
    {
        $rules = [
            'status' => 'required|string',
        ];

        // Tambahkan validasi, tapi izinkan nullable karena bisa auto-isi dari data sebelumnya
        if ($request->status === 'Pergi') {
            $rules['nama_pemakai'] = 'nullable|string';
            $rules['departemen'] = 'nullable|string';
            $rules['driver'] = 'nullable|string';
            $rules['tujuan'] = 'nullable|string';
            $rules['keterangan'] = 'nullable|string';
        }

        $request->validate($rules);

        $kendaraan = Kendaraan::findOrFail($request->id);

        // Ambil data history terakhir sebelum kendaraan jadi "Stand By"
        $lastPergi = HistoryKendaraan::where('kendaraan_id', $kendaraan->id)
            ->where('status', 'Pergi')
            ->latest()
            ->first();

        // Isi nilai dari request atau dari data sebelumnya (kendaraan/history)
        $namaPemakai = $request->nama_pemakai
            ?? $kendaraan->nama_pemakai
            ?? $lastPergi?->nama_pemakai;

        $departemen = $request->departemen
            ?? $kendaraan->departemen
            ?? $lastPergi?->departemen;

        $driver = $request->driver
            ?? $kendaraan->driver
            ?? $lastPergi?->driver;

        $tujuan = $request->tujuan
            ?? $kendaraan->tujuan
            ?? $lastPergi?->tujuan;

        $keterangan = $request->keterangan
            ?? $kendaraan->keterangan
            ?? $lastPergi?->keterangan;

        // Simpan ke history sebelum update
        HistoryKendaraan::create([
            'kendaraan_id' => $kendaraan->id,
            'status' => $request->status,
            'nama_mobil' => $request->nama_mobil,
            'nopol' => $request->nopol,
            'nama_pemakai' => $namaPemakai,
            'departemen' => $departemen,
            'driver' => $driver,
            'tujuan' => $tujuan,
            'keterangan' => $keterangan,
            'pic_update' => auth()->user()->username,
        ]);

        $kendaraan->status = $request->status;

        if ($request->status === 'Pergi') {
            // Isi data jika status Pergi
            $kendaraan->nama_pemakai = $namaPemakai;
            $kendaraan->departemen = $departemen;
            $kendaraan->driver = $driver;
            $kendaraan->tujuan = $tujuan;
            $kendaraan->keterangan = $keterangan;
        } else {
            // Reset semua jika Stand By
            $kendaraan->nama_pemakai = null;
            $kendaraan->departemen = null;
            $kendaraan->driver = null;
            $kendaraan->tujuan = null;
            $kendaraan->keterangan = null;
        }

        $kendaraan->updated_at = now();
        $kendaraan->save();

        return response()->json([
            'success' => true,
            'message' => "Status kendaraan <strong>{$kendaraan->nama_mobil} {$kendaraan->nopol}</strong> berhasil diperbarui!",
            'status' => $kendaraan->status,
        ]);
    }

    public function historyKendaraan()
    {
        return view('kendaraan.history');
    }

    public function getDatahistoryKendaraan()
    {
        $data = HistoryKendaraan::select([
            'id',
            'updated_at',
            'nama_mobil',
            'nopol',
            'status',
            'nama_pemakai',
            'departemen',
            'driver',
            'tujuan',
            'keterangan',
            'pic_update',
        ])
        ->orderBy('updated_at', 'desc')
        ->get();

        return response()->json($data);
    }
}
