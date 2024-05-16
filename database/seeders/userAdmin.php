<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class userAdmin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
        'name' => "Jose Bras",
        'email' => "jose@gmail.com",
        'password' => Hash::make("giajttjh89y"),
        'scope' => 0,
        'ativo' => true,
        'admin' => true
        ]);
    }
}
