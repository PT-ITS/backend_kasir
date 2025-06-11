<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Langganan;
use App\Models\Invoice;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'no_hp' => '79079070s',
            'level' => '1',
            'status' => '1',
            'password' => Hash::make('12345'),
        ]);
    }
}
