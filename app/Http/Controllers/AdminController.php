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
        $data = User::orderBy('updated_at', 'desc')->get();
        return response()->json($data);
    }

    public function tambahUsers(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'jabatan' => 'required',
            'password' => 'required|min:3'
        ], [
            'username.unique' => 'Username sudah ada.',
            'username.required' => 'Username wajib diisi.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 3 karakter.'
        ]);

        User::create([
            'username' => $request->username,
            'jabatan' => $request->jabatan,
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['message' => 'User berhasil ditambahkan!']);
    }

    public function gantiPassword(Request $request)
    {
        // Validasi input
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:3|confirmed', // Validasi untuk new_password dan konfirmasinya
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 3 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);
    
        // Menemukan user berdasarkan ID
        $user = User::find($request->user_id);
    
        // Memeriksa apakah password saat ini cocok
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.'])->withInput();
        }
    
        // Mengubah password pengguna dengan password baru
        $user->password = Hash::make($request->new_password);
        $user->save();
    
        // Mengirimkan respons sukses
        return response()->json(['message' => 'Password berhasil diperbarui!']);
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
