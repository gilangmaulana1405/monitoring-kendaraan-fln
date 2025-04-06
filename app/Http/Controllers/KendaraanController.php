<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use Illuminate\Http\Request;
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
        });
        return view('kendaraan.index', compact('kendaraan'));
    }

    public function getData()
    {
        $kendaraan = Kendaraan::all()->map(function ($k) {
            $imgPath = public_path('img/');
            $extensions = ['jpeg', 'jpg', 'png', 'webp'];
            $image = 'img/default.png'; // Gunakan path relatif

            foreach ($extensions as $ext) {
                if (File::exists($imgPath . $k->nopol . '.' . $ext)) {
                    $image = 'img/' . $k->nopol . '.' . $ext; // Path relatif ke public/
                    break;
                }
            }

            $k->image_path = url($image); // Gunakan URL lengkap Laravel
            return $k;
        });

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
        });

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
