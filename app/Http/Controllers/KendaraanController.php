<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class KendaraanController extends Controller
{

    // monitoring kendaraan
    public function index()
    {
        $kendaraan = Kendaraan::all()->map(function ($k) {
            $imgPath = public_path('img/');
            $extensions = ['jpeg', 'jpg', 'png', 'webp'];
            $image = 'img/default.png';

            foreach ($extensions as $ext) {
                if (File::exists($imgPath . $k->nopol . '.' . $ext)) {
                    $image = 'img/' . $k->nopol . '.' . $ext;
                    break;
                }
            }
            $k->image_path = $image;
            return $k;
        })->sortBy(function ($item) {
            return match ($item->status) {
                'Stand By'  => 1,
                'Pergi'     => 2,
                'Perbaikan' => 3,
            };
        })->values();

        return view('kendaraan.index', compact('kendaraan'));
    }

    public function getData()
    {
        $kendaraan = Kendaraan::all()->map(function ($k) {
        $imgPath = public_path('img/');
        $extensions = ['jpeg', 'jpg', 'png', 'webp'];
        $image = 'img/default.png';

        foreach ($extensions as $ext) {
            if (File::exists($imgPath . $k->nopol . '.' . $ext)) {
                $image = 'img/' . $k->nopol . '.' . $ext;
                break;
            }
        }

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
    })->sortBy(function ($item) {
        return match ($item['status']) {
            'Stand By'  => 1,
            'Pergi'     => 2,
            'Perbaikan' => 3,
            default     => 4,
        };
    })->values();

    
    return response()->json($kendaraan);
    }

    // list kendaraan untuk update data
    public function kendaraan()
    {
        $kendaraan = Kendaraan::all()->map(function ($k) {
            $imgPath = public_path('img/');
            $extensions = ['jpeg', 'jpg', 'png', 'webp'];
            $image = 'img/default.png';

            foreach ($extensions as $ext) {
                if (File::exists($imgPath . $k->nopol . '.' . $ext)) {
                    $image = 'img/' . $k->nopol . '.' . $ext;
                    break;
                }
            }

            $k->image_path = $image;
            return $k;
        })->sortBy(function ($item) {
            return match ($item->status) {
                'Stand By'  => 1,
                'Pergi'     => 2,
                'Perbaikan' => 3,
            };
        })->values();;

        return view('kendaraan.overview', compact('kendaraan'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'status' => 'required|string',
            'nama_pemakai' => 'nullable|string',
            'departemen' => 'nullable|string',
            'tujuan' => 'nullable|string',
        ]);

        $kendaraan = Kendaraan::findOrFail($request->id);
        $kendaraan->status = $request->status;
        $kendaraan->nama_pemakai = $request->nama_pemakai;
        $kendaraan->departemen = $request->departemen;

        // Simpan tujuan hanya jika status "Pergi"
        if ($request->status == "Pergi") {
            $kendaraan->driver = $request->driver;
            $kendaraan->tujuan = $request->tujuan;
            $kendaraan->keterangan = $request->keterangan;
        } else {
            $kendaraan->nama_pemakai = null;
            $kendaraan->departemen = null;
            $kendaraan->driver = null;
            $kendaraan->tujuan = null;
            $kendaraan->keterangan = null;
        }

        $kendaraan->save();

        return response()->json([
            'success' => true,
            'message' => "Status kendaraan <strong>{$kendaraan->nama_mobil} {$kendaraan->nopol}</strong> berhasil diperbarui!"
        ]);
    }
}
