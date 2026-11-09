<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['username' => 'arjmnz'], [
            'full_name' => 'Arwind Craig R. Jimenez',
            'email' => 'arjimenez@feualabang.edu.ph',
        ]);
    }
}
