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
            ['username' => 'flnga', 'password' => Hash::make('123'), 'jabatan' => 'Admin GA'],
            ['username' => 'widiartip', 'password' => Hash::make('123'), 'jabatan' => 'Staff GA'],
            ['username' => 'ades', 'password' => Hash::make('123'), 'jabatan' => 'Staff GA'],
            ['username' => 'nurhidayatullah', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'nilamsantikayani', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'abubakar', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'wiryawijaya', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'uuttrisafain', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'ariadhari', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'ahmadjayasaputra', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'dedeyusup', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'fajarnugraha', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'supardi', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'hafizramadhan', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'ajipuji', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'bagasedi', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'fajarsidik', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'sunansusanto', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'erwin', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'kemalsuhayat', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'warman', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'diartamat', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'johanessouhoka', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'ripanpradesh', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'andiyuhandi', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'wahyuputra', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
            ['username' => 'aguspurnairawan', 'password' => Hash::make('123'), 'jabatan' => 'Security'],
        ];

        DB::table('users')->insert($users);
    }
}
