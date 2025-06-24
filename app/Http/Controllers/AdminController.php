<?php

namespace App\Http\Controllers;

use Image;
use App\Models\User;
use App\Models\Kendaraan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\HistoryKendaraan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
        $data = HistoryKendaraan::with('kendaraan') // join relasi
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($item) {
                // Pastikan kendaraan tersedia (relasi tidak null)
                $namaMobil = $item->kendaraan->nama_mobil ?? '-';
                $nopol = $item->kendaraan->nopol ?? '-';
                $status = $item->kendaraan->status ?? '-';

                // Gabungkan nama mobil dan nopol
                $item->mobil = $namaMobil . '<br>(' . $nopol . ')';

                // Gabungkan nama pemakai dan departemen
                $item->pemakai = ($item->nama_pemakai ?? '-') . ' <br> ' . ($item->departemen ?? '-');

                // Tambahkan properti status untuk badge di view
                $item->status = $status;

                return $item;
            });

        return response()->json($data);
    }


    public function listUsers()
    {
        $users = User::all();
        $jabatanList = ['Admin GA', 'Staff GA', 'Security'];
        return view('admin.users', compact('users', 'jabatanList'));
    }

    public function getDataUsers()
    {
        $data = User::where('isActive', 1)
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($data);
    }

    public function tambahUsers(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'username' => 'required|unique:users,username|min:3',
            'jabatan' => 'required',
            'password' => 'required|min:6'
        ], [
            'username.unique' => 'Username sudah ada.',
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.'
        ]);

        User::create([
            'nama_lengkap' => ucwords(strtolower($request->nama_lengkap)),
            'username' => $request->username,
            'jabatan' => $request->jabatan,
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['message' => 'User berhasil ditambahkan!']);
    }

    public function editUsers(Request $request, $id)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'username' => 'required|unique:users,username|min:3',
            'jabatan' => 'required'
        ], [
            'username.unique' => 'Username sudah ada.',
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'jabatan.required' => 'Jabatan wajib diisi.',
        ]);

        $user = User::findOrFail($id);
        $user->nama_lengkap = ucwords(strtolower($request->nama_lengkap));
        $user->username = $request->username;
        $user->jabatan = $request->jabatan;
        $user->save();

        return response()->json(['message' => 'User berhasil diperbarui!']);
    }

    public function hapusUsers($id)
    {
        $user = User::findOrFail($id);
        $user->isActive = 0;
        $user->save();

        return response()->json(['message' => 'User berhasil dihapus!']);
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
        $kendaraans = Kendaraan::all();
        return view('admin.kendaraan', compact('kendaraans'));
    }

    public function getDataKendaraan()
    {
        $data = Kendaraan::select('id', 'nama_mobil', 'nopol', 'gambar_mobil')
            ->orderBy('created_at', 'desc')
            ->get();

        $data->transform(function ($item) {
            if ($item->gambar_mobil) {
                $item->gambar_url = asset('storage/mobil/' . $item->gambar_mobil);
            } else {
                $item->gambar_url = null;
            }
            return $item;
        });

        return response()->json($data);
    }

    public function tambahKendaraan(Request $request)
    {
        $request->validate([
            'nama_mobil' => 'required|string|max:255',
            'nopol' => 'required|string|max:255|unique:kendaraans,nopol',
            'gambar_mobil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'nopol.unique' => 'Error : Nopol kendaraan telah tersedia!',
            'gambar_mobil.max' => 'Error : Ukuran gambar maksimal 2MB.',
            'gambar_mobil.mimes' => 'Error : Format gambar harus jpeg, png, atau jpg.',
            'gambar_mobil.image' => 'Error : File yang diunggah harus berupa gambar.',
        ]);

        $nama_mobil = ucwords(strtolower($request->nama_mobil));
        $nopol = strtoupper($request->nopol);

        $gambarPath = null;

        if ($request->hasFile('gambar_mobil')) {
            $file = $request->file('gambar_mobil');

            $extension = $file->getClientOriginalExtension();
            $filename = "{$nama_mobil}_{$nopol}.{$extension}";

            // Buat image instance
            $image = Image::make($file->getPathname());

            // Resize max width 1024 px
            $image->resize(1024, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Simpan gambar ke temporary path (memory) dulu
            $tempPath = sys_get_temp_dir() . '/' . $filename;
            $quality = 90;
            $image->save($tempPath, $quality);

            // Kompres sampai <= 1MB atau quality minimal 30
            while (filesize($tempPath) > 1024 * 1024 && $quality >= 30) {
                $quality -= 5;
                $image->save($tempPath, $quality);
            }

            $gambarPath = $filename;

            Storage::disk('public')->put("mobil/{$filename}", file_get_contents($tempPath));

            // Hapus file temporary
            unlink($tempPath);
        }

        $kendaraan = Kendaraan::create([
            'nama_mobil' => $nama_mobil,
            'nopol' => $nopol,
            'gambar_mobil' => $gambarPath,
            'status' => 'Stand By',
        ]);

        return response()->json(['message' => 'Kendaraan berhasil ditambahkan!']);
    }
    public function editKendaraan(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:kendaraans,id',
            'nama_mobil' => 'required|string|max:255',
            'nopol' => 'required|string|max:255|unique:kendaraans,nopol,' . $request->id,
            'gambar_mobil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'nopol.unique' => 'Error : Nopol kendaraan telah tersedia!',
            'gambar_mobil.max' => 'Error : Ukuran gambar maksimal 2MB.',
            'gambar_mobil.mimes' => 'Error : Format gambar harus jpeg, png, atau jpg.',
            'gambar_mobil.image' => 'Error : File yang diunggah harus berupa gambar.',
        ]);

        $kendaraan = Kendaraan::findOrFail($request->id);

        $nama_mobil = ucwords(strtolower($request->nama_mobil));
        $nopol = strtoupper($request->nopol);

        $gambarPath = $kendaraan->gambar_mobil;

        if ($request->hasFile('gambar_mobil')) {
            // Hapus gambar lama jika ada
            if ($kendaraan->gambar_mobil && Storage::disk('public')->exists("mobil/{$kendaraan->gambar_mobil}")) {
                Storage::disk('public')->delete("mobil/{$kendaraan->gambar_mobil}");
            }

            $file = $request->file('gambar_mobil');
            $extension = $file->getClientOriginalExtension();
            $filename = "{$nama_mobil}_{$nopol}." . $extension;

            $image = \Image::make($file->getPathname());
            $image->resize(1024, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $tempPath = sys_get_temp_dir() . '/' . $filename;
            $quality = 90;
            $image->save($tempPath, $quality);

            while (filesize($tempPath) > 1024 * 1024 && $quality >= 30) {
                $quality -= 5;
                $image->save($tempPath, $quality);
            }

            // Simpan path baru
            $gambarPath = $filename;
            Storage::disk('public')->put("mobil/{$filename}", file_get_contents($tempPath));
            unlink($tempPath);
        }

        $kendaraan->update([
            'nama_mobil' => $nama_mobil,
            'nopol' => $nopol,
            'gambar_mobil' => $gambarPath,
        ]);

        return response()->json(['message' => 'Kendaraan berhasil diperbarui.']);
    }
    public function hapusKendaraan($id)
    {
        $user = Kendaraan::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Kendaraan berhasil dihapus!']);
    }
}
