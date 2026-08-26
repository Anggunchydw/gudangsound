<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Administrator;
use Dcat\Admin\Models\Role;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminPassword = config('services.initial_credentials.admin_password');
        $ownerPassword = config('services.initial_credentials.owner_password');

        if (empty($adminPassword) || strlen($adminPassword) < 12) {
            throw new RuntimeException(
                'INITIAL_ADMIN_PASSWORD wajib diatur di file .env dan memiliki panjang minimal 12 karakter.'
            );
        }

        if (empty($ownerPassword) || strlen($ownerPassword) < 12) {
            throw new RuntimeException(
                'INITIAL_OWNER_PASSWORD wajib diatur di file .env dan memiliki panjang minimal 12 karakter.'
            );
        }

        $admin = Administrator::firstOrCreate(
            ['username' => 'admin'],
            [
                'name'     => 'Administrator',
                'email'    => 'a@gmail.com',
                'password' => bcrypt($adminPassword),
            ]
        );

        if ($role = Role::where('slug', 'administrator')->first()) {
            $admin->roles()->sync([$role->id]);
        }

        $pemilik = Administrator::firstOrCreate(
            ['username' => 'pemilik'],
            [
                'name'     => 'Pemilik',
                'email'    => 'p@gmail.com',
                'password' => bcrypt($ownerPassword),
            ]
        );

        if ($role = Role::where('slug', 'pemilik')->first()) {
            $pemilik->roles()->sync([$role->id]);
        }
    }
}
