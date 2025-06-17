<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Langganan;
use App\Models\Invoice;
use App\Models\Kasir;
use App\Models\Manager;
use App\Models\Toko;
use Illuminate\Support\Facades\Hash;

class DataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Owner
        User::create([
            'name' => 'owner',
            'email' => 'owner@gmail.com',
            'level' => '0',
            'status' => '1',
            'password' => Hash::make('12345'),
        ]);

        // Manager
        User::create([
            'name' => 'manager',
            'email' => 'manager@gmail.com',
            'level' => '1',
            'status' => '1',
            'password' => Hash::make('12345'),
        ]);
        Manager::create([
            'nama_manager' => 'Nama Manager',
            'hp_manager' => '081',
            'alamat_manager' => 'Alamat Manager',
            'fk_id_user' => '2',
        ]);

        // Toko
        Toko::create([
            'nama_toko' => 'Toko 1',
            'hp_toko' => '081',
            'alamat_toko' => 'Alamat Toko',
            'fk_id_manager' => '2',
        ]);
        Toko::create([
            'nama_toko' => 'Toko 2',
            'hp_toko' => '081',
            'alamat_toko' => 'Alamat Toko',
            'fk_id_manager' => '2',
        ]);

        // Kasir
        User::create([
            'name' => 'kasir1',
            'email' => 'kasir1@gmail.com',
            'level' => '2',
            'status' => '1',
            'password' => Hash::make('12345'),
        ]);
        Kasir::create([
            'nama_kasir' => 'Nama Kasir 1',
            'hp_kasir' => '081',
            'alamat_kasir' => 'Alamat Kasir',
            'fk_id_toko' => '1',
            'fk_id_user' => '3',
        ]);
        // Kasir
        User::create([
            'name' => 'kasir2',
            'email' => 'kasir2@gmail.com',
            'level' => '2',
            'status' => '1',
            'password' => Hash::make('12345'),
        ]);
        Kasir::create([
            'nama_kasir' => 'Kasir 2',
            'hp_kasir' => '081',
            'alamat_kasir' => 'Alamat Kasir',
            'fk_id_toko' => '2',
            'fk_id_user' => '4',
        ]);
    }
}
