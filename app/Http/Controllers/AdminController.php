<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use App\Models\HistoryKendaraan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function historyKendaraan()
    {
        return view('admin.history');
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
            ->get()
            ->map(function ($item) {
                // Gabungkan nama mobil dan nopol
                $item->mobil = $item->nama_mobil . '<br>' . ' (' . $item->nopol . ')';

                // Gabungkan nama pemakai dan departemen
                $item->pemakai = $item->nama_pemakai . ' <br> ' . $item->departemen;

                return $item;
            });

        return response()->json($data);
    }

    public function listUsers()
    {
        return view('admin.users');
    }

    public function getDataUsers()
    {
        $data = User::all();
        return response()->json($data);
    }

    public function gantiPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'current_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::find($request->user_id);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.'])->withInput();
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    public function listKendaraan()
    {
        return view('admin.kendaraan');
    }

    public function getDataKendaraan()
    {
        $data = Kendaraan::select('nama_mobil', 'nopol', 'gambar_mobil')->get();
        return response()->json($data);
    }
}
