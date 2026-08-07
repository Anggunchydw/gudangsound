<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Administrator;
use Dcat\Admin\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Administrator
        $admin = Administrator::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('admin'),
            ]
        );

        if ($role = Role::where('slug', 'administrator')->first()) {
            $admin->roles()->sync([$role->id]);
        }

        // Pemilik
        $pemilik = Administrator::firstOrCreate(
            ['username' => 'pemilik'],
            [
                'name' => 'Pemilik',
                'email' => 'pemilik@gmail.com',
                'password' => bcrypt('pemilik'),
            ]
        );

        if ($role = Role::where('slug', 'pemilik')->first()) {
            $pemilik->roles()->sync([$role->id]);
        }
    }
}
