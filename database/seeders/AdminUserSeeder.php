<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Administrator;
use Dcat\Admin\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $admin = Administrator::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('admin'),
            ]
        );

        $role = Role::where('slug', 'administrator')->first();

        if ($role) {
            $admin->roles()->sync([$role->id]);
        }
    }
}
