<?php

namespace Database\Seeders;

use Dcat\Admin\Models;
use Illuminate\Database\Seeder;
use DB;

class AdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // base tables
        Models\Menu::truncate();
        Models\Menu::insert(
            [
                [
                    "id" => 1,
                    "parent_id" => 0,
                    "order" => 9,
                    "title" => "Index",
                    "icon" => "feather icon-bar-chart-2",
                    "uri" => "/",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:33:02",
                    "updated_at" => "2026-06-19 05:57:16"
                ],
                [
                    "id" => 2,
                    "parent_id" => 0,
                    "order" => 10,
                    "title" => "Admin",
                    "icon" => "feather icon-settings",
                    "uri" => "",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:33:02",
                    "updated_at" => "2026-06-19 05:57:16"
                ],
                [
                    "id" => 3,
                    "parent_id" => 2,
                    "order" => 11,
                    "title" => "Users",
                    "icon" => "",
                    "uri" => "auth/users",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:33:02",
                    "updated_at" => "2026-06-19 05:57:16"
                ],
                [
                    "id" => 4,
                    "parent_id" => 2,
                    "order" => 12,
                    "title" => "Roles",
                    "icon" => "",
                    "uri" => "auth/roles",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:33:02",
                    "updated_at" => "2026-06-19 05:57:16"
                ],
                [
                    "id" => 5,
                    "parent_id" => 2,
                    "order" => 13,
                    "title" => "Permission",
                    "icon" => "",
                    "uri" => "auth/permissions",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:33:02",
                    "updated_at" => "2026-06-19 05:57:16"
                ],
                [
                    "id" => 6,
                    "parent_id" => 2,
                    "order" => 14,
                    "title" => "Menu",
                    "icon" => "",
                    "uri" => "auth/menu",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:33:02",
                    "updated_at" => "2026-06-19 05:57:16"
                ],
                [
                    "id" => 7,
                    "parent_id" => 2,
                    "order" => 15,
                    "title" => "Extensions",
                    "icon" => "",
                    "uri" => "auth/extensions",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:33:02",
                    "updated_at" => "2026-06-19 05:57:16"
                ],
                [
                    "id" => 8,
                    "parent_id" => 0,
                    "order" => 1,
                    "title" => "Barang",
                    "icon" => "fa-inbox",
                    "uri" => "/barang",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:40:03",
                    "updated_at" => "2026-06-19 05:43:43"
                ],
                [
                    "id" => 9,
                    "parent_id" => 0,
                    "order" => 2,
                    "title" => "Paket",
                    "icon" => "fa-dropbox",
                    "uri" => "/paket",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:43:30",
                    "updated_at" => "2026-06-19 05:43:43"
                ],
                [
                    "id" => 10,
                    "parent_id" => 0,
                    "order" => 3,
                    "title" => "Penyewaan",
                    "icon" => "fa-cart-arrow-down",
                    "uri" => "/penyewaan",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:47:34",
                    "updated_at" => "2026-06-19 05:57:16"
                ],
                [
                    "id" => 11,
                    "parent_id" => 13,
                    "order" => 6,
                    "title" => "Pemasukan",
                    "icon" => "fa-line-chart",
                    "uri" => "/pemasukan",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:48:37",
                    "updated_at" => "2026-06-19 05:57:16"
                ],
                [
                    "id" => 12,
                    "parent_id" => 13,
                    "order" => 7,
                    "title" => "Pengeluaran",
                    "icon" => "fa-bar-chart-o",
                    "uri" => "/pengeluaran",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:51:25",
                    "updated_at" => "2026-06-19 05:57:16"
                ],
                [
                    "id" => 13,
                    "parent_id" => 0,
                    "order" => 5,
                    "title" => "Keuangan",
                    "icon" => "fa-money",
                    "uri" => NULL,
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:53:09",
                    "updated_at" => "2026-06-19 05:57:16"
                ],
                [
                    "id" => 14,
                    "parent_id" => 13,
                    "order" => 8,
                    "title" => "Rekap Keuangan",
                    "icon" => "fa-cc",
                    "uri" => "/rekap-keuangan",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:54:44",
                    "updated_at" => "2026-06-19 05:57:16"
                ],
                [
                    "id" => 15,
                    "parent_id" => 0,
                    "order" => 4,
                    "title" => "Jadwal Acara",
                    "icon" => "fa-calendar-check-o",
                    "uri" => "/Jadwal-Acara",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:56:10",
                    "updated_at" => "2026-06-19 05:57:16"
                ]
            ]
        );

        Models\Permission::truncate();
        Models\Permission::insert(
            [
                [
                    "id" => 1,
                    "name" => "Auth management",
                    "slug" => "auth-management",
                    "http_method" => "",
                    "http_path" => "",
                    "order" => 1,
                    "parent_id" => 0,
                    "created_at" => "2026-06-19 05:33:02",
                    "updated_at" => NULL
                ],
                [
                    "id" => 2,
                    "name" => "Users",
                    "slug" => "users",
                    "http_method" => "",
                    "http_path" => "/auth/users*",
                    "order" => 2,
                    "parent_id" => 1,
                    "created_at" => "2026-06-19 05:33:02",
                    "updated_at" => NULL
                ],
                [
                    "id" => 3,
                    "name" => "Roles",
                    "slug" => "roles",
                    "http_method" => "",
                    "http_path" => "/auth/roles*",
                    "order" => 3,
                    "parent_id" => 1,
                    "created_at" => "2026-06-19 05:33:02",
                    "updated_at" => NULL
                ],
                [
                    "id" => 4,
                    "name" => "Permissions",
                    "slug" => "permissions",
                    "http_method" => "",
                    "http_path" => "/auth/permissions*",
                    "order" => 4,
                    "parent_id" => 1,
                    "created_at" => "2026-06-19 05:33:02",
                    "updated_at" => NULL
                ],
                [
                    "id" => 5,
                    "name" => "Menu",
                    "slug" => "menu",
                    "http_method" => "",
                    "http_path" => "/auth/menu*",
                    "order" => 5,
                    "parent_id" => 1,
                    "created_at" => "2026-06-19 05:33:02",
                    "updated_at" => NULL
                ],
                [
                    "id" => 6,
                    "name" => "Extension",
                    "slug" => "extension",
                    "http_method" => "",
                    "http_path" => "/auth/extensions*",
                    "order" => 6,
                    "parent_id" => 1,
                    "created_at" => "2026-06-19 05:33:02",
                    "updated_at" => NULL
                ]
            ]
        );

        Models\Role::truncate();
        Models\Role::insert(
            [
                [
                    "id" => 1,
                    "name" => "Administrator",
                    "slug" => "administrator",
                    "created_at" => "2026-06-19 05:33:02",
                    "updated_at" => "2026-06-19 05:33:02"
                ]
            ]
        );

        Models\Setting::truncate();
        Models\Setting::insert(
            []
        );

        Models\Extension::truncate();
        Models\Extension::insert(
            []
        );

        Models\ExtensionHistory::truncate();
        Models\ExtensionHistory::insert(
            []
        );

        // pivot tables
        DB::table('admin_permission_menu')->truncate();
        DB::table('admin_permission_menu')->insert(
            []
        );

        DB::table('admin_role_menu')->truncate();
        DB::table('admin_role_menu')->insert(
            [
                [
                    "role_id" => 1,
                    "menu_id" => 8,
                    "created_at" => "2026-06-19 05:40:03",
                    "updated_at" => "2026-06-19 05:40:03"
                ],
                [
                    "role_id" => 1,
                    "menu_id" => 9,
                    "created_at" => "2026-06-19 05:43:30",
                    "updated_at" => "2026-06-19 05:43:30"
                ],
                [
                    "role_id" => 1,
                    "menu_id" => 10,
                    "created_at" => "2026-06-19 05:47:34",
                    "updated_at" => "2026-06-19 05:47:34"
                ],
                [
                    "role_id" => 1,
                    "menu_id" => 11,
                    "created_at" => "2026-06-19 05:48:37",
                    "updated_at" => "2026-06-19 05:48:37"
                ],
                [
                    "role_id" => 1,
                    "menu_id" => 14,
                    "created_at" => "2026-06-19 05:54:44",
                    "updated_at" => "2026-06-19 05:54:44"
                ]
            ]
        );

        DB::table('admin_role_permissions')->truncate();
        DB::table('admin_role_permissions')->insert(
            []
        );
        Models\Administrator::truncate();

        Models\Administrator::create([
            'id' => 1,
            'username' => 'admin',
            'password' => bcrypt('admin'),
            'name' => 'Administrator',
        ]);
        DB::table('admin_role_users')->truncate();

        DB::table('admin_role_users')->insert([
            [
                'role_id' => 1,
                'user_id' => 1,
            ]
        ]);

        // finish
    }
}
