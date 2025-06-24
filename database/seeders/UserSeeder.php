<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['username' => 'flnga', 'nama_lengkap' => 'Flnga', 'password' => Hash::make('123456'), 'jabatan' => 'Admin GA'],
            ['username' => 'widiartip', 'nama_lengkap' => 'Widiartip', 'password' => Hash::make('123456'), 'jabatan' => 'Staff GA'],
            ['username' => 'ades', 'nama_lengkap' => 'Ades', 'password' => Hash::make('123456'), 'jabatan' => 'Staff GA'],
            ['username' => 'nurhidayatullah', 'nama_lengkap' => 'Nur Hidayatullah', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'nilamsantikayani', 'nama_lengkap' => 'Nilam Santikayani', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'abubakar', 'nama_lengkap' => 'Abu Bakar', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'wiryawijaya', 'nama_lengkap' => 'Wirya Wijaya', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'uuttrisafain', 'nama_lengkap' => 'Uut Trisafain', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'ariadhari', 'nama_lengkap' => 'Aria Dhari', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'ahmadjayasaputra', 'nama_lengkap' => 'Ahmad Jaya Saputra', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'dedeyusup', 'nama_lengkap' => 'Dede Yusuf', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'fajarnugraha', 'nama_lengkap' => 'Fajar Nugraha', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'supardi', 'nama_lengkap' => 'Supardi', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'hafizramadhan', 'nama_lengkap' => 'Hafiz Ramadhan', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'ajipuji', 'nama_lengkap' => 'Aji Puji', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'bagasedi', 'nama_lengkap' => 'Bagas Edi', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'fajarsidik', 'nama_lengkap' => 'Fajar Sidik', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'sunansusanto', 'nama_lengkap' => 'Sunan Susanto', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'erwin', 'nama_lengkap' => 'Erwin', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'kemalsuhayat', 'nama_lengkap' => 'Kemal Suhayat', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'warman', 'nama_lengkap' => 'Warman', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'diartamat', 'nama_lengkap' => 'Diarta Mat', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'johanessouhoka', 'nama_lengkap' => 'Johanes Souhoka', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'ripanpradesh', 'nama_lengkap' => 'Ripan Pradesh', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'andiyuhandi', 'nama_lengkap' => 'Andi Yuhandi', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'wahyuputra', 'nama_lengkap' => 'Wahyu Putra', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'aguspurnairawan', 'nama_lengkap' => 'Agus Purnairawan', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
        ];

        DB::table('users')->insert($users);
    }
}
