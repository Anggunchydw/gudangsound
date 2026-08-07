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
                    "order" => 12,
                    "title" => "Index",
                    "icon" => "feather icon-bar-chart-2",
                    "uri" => "/",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:33:02",
                    "updated_at" => "2026-07-17 06:34:25"
                ],
                [
                    "id" => 2,
                    "parent_id" => 0,
                    "order" => 13,
                    "title" => "Admin",
                    "icon" => "feather icon-settings",
                    "uri" => NULL,
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:33:02",
                    "updated_at" => "2026-07-17 06:34:25"
                ],
                [
                    "id" => 3,
                    "parent_id" => 2,
                    "order" => 14,
                    "title" => "Users",
                    "icon" => "",
                    "uri" => "auth/users",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:33:02",
                    "updated_at" => "2026-07-17 06:34:25"
                ],
                [
                    "id" => 4,
                    "parent_id" => 2,
                    "order" => 15,
                    "title" => "Roles",
                    "icon" => "",
                    "uri" => "auth/roles",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:33:02",
                    "updated_at" => "2026-07-17 06:34:25"
                ],
                [
                    "id" => 5,
                    "parent_id" => 2,
                    "order" => 16,
                    "title" => "Permission",
                    "icon" => "",
                    "uri" => "auth/permissions",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:33:02",
                    "updated_at" => "2026-07-17 06:34:25"
                ],
                [
                    "id" => 6,
                    "parent_id" => 2,
                    "order" => 17,
                    "title" => "Menu",
                    "icon" => "",
                    "uri" => "auth/menu",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:33:02",
                    "updated_at" => "2026-07-17 06:34:25"
                ],
                [
                    "id" => 7,
                    "parent_id" => 2,
                    "order" => 18,
                    "title" => "Extensions",
                    "icon" => "",
                    "uri" => "auth/extensions",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:33:02",
                    "updated_at" => "2026-07-17 06:34:25"
                ],
                [
                    "id" => 8,
                    "parent_id" => 0,
                    "order" => 1,
                    "title" => "Barang",
                    "icon" => "fa-cube",
                    "uri" => "/barang",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:40:03",
                    "updated_at" => "2026-07-17 06:30:16"
                ],
                [
                    "id" => 9,
                    "parent_id" => 0,
                    "order" => 2,
                    "title" => "Paket",
                    "icon" => "fa-cubes",
                    "uri" => "/paket",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-06-19 05:43:30",
                    "updated_at" => "2026-07-17 06:30:30"
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
                ],
                [
                    "id" => 16,
                    "parent_id" => 0,
                    "order" => 9,
                    "title" => "Pengguna",
                    "icon" => "fa-user",
                    "uri" => "pengguna",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-07-15 17:29:56",
                    "updated_at" => "2026-07-15 21:28:49"
                ],
                [
                    "id" => 17,
                    "parent_id" => 0,
                    "order" => 10,
                    "title" => "Penugasan",
                    "icon" => "fa-calendar-plus-o",
                    "uri" => "/penugasan",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-07-16 15:40:27",
                    "updated_at" => "2026-07-16 15:40:42"
                ],
                [
                    "id" => 18,
                    "parent_id" => 0,
                    "order" => 11,
                    "title" => "Kondisi Barang",
                    "icon" => "fa-cog",
                    "uri" => "/kondisi-barang",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2026-07-17 06:33:57",
                    "updated_at" => "2026-07-17 06:34:25"
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
                ],
                [
                    "id" => 7,
                    "name" => "Barang",
                    "slug" => "barang",
                    "http_method" => "",
                    "http_path" => "/barang*",
                    "order" => 7,
                    "parent_id" => 0,
                    "created_at" => "2026-07-15 17:46:33",
                    "updated_at" => "2026-07-15 18:40:45"
                ],
                [
                    "id" => 8,
                    "name" => "Paket",
                    "slug" => "paket",
                    "http_method" => "",
                    "http_path" => "/paket*",
                    "order" => 8,
                    "parent_id" => 0,
                    "created_at" => "2026-07-15 17:47:09",
                    "updated_at" => "2026-07-15 18:43:29"
                ],
                [
                    "id" => 9,
                    "name" => "Penyewaan",
                    "slug" => "penyewaan",
                    "http_method" => "",
                    "http_path" => "/penyewaan*",
                    "order" => 9,
                    "parent_id" => 0,
                    "created_at" => "2026-07-15 17:47:24",
                    "updated_at" => "2026-07-15 18:43:55"
                ],
                [
                    "id" => 10,
                    "name" => "Jadwal Acara",
                    "slug" => "jadwal",
                    "http_method" => "",
                    "http_path" => "/Jadwal-Acara*",
                    "order" => 10,
                    "parent_id" => 0,
                    "created_at" => "2026-07-15 17:47:50",
                    "updated_at" => "2026-07-15 18:47:00"
                ],
                [
                    "id" => 11,
                    "name" => "Keuangan",
                    "slug" => "keuangan",
                    "http_method" => "",
                    "http_path" => "/pemasukan*,/pengeluaran*,/rekap-keuangan*",
                    "order" => 11,
                    "parent_id" => 0,
                    "created_at" => "2026-07-15 17:48:30",
                    "updated_at" => "2026-07-15 18:50:07"
                ],
                [
                    "id" => 12,
                    "name" => "Pengguna",
                    "slug" => "pengguna",
                    "http_method" => "",
                    "http_path" => "/pengguna*",
                    "order" => 12,
                    "parent_id" => 0,
                    "created_at" => "2026-07-15 17:48:57",
                    "updated_at" => "2026-07-15 21:30:58"
                ],
                [
                    "id" => 13,
                    "name" => "Penugasan",
                    "slug" => "penugasan",
                    "http_method" => "",
                    "http_path" => "/penugasan*",
                    "order" => 13,
                    "parent_id" => 0,
                    "created_at" => "2026-07-15 17:49:27",
                    "updated_at" => "2026-07-17 05:42:41"
                ],
                [
                    "id" => 14,
                    "name" => "Dashboard",
                    "slug" => "dashboard",
                    "http_method" => "",
                    "http_path" => "",
                    "order" => 14,
                    "parent_id" => 0,
                    "created_at" => "2026-07-15 17:57:38",
                    "updated_at" => "2026-07-15 17:57:38"
                ],
                [
                    "id" => 15,
                    "name" => "kondisi barang",
                    "slug" => "kondisi barang",
                    "http_method" => "",
                    "http_path" => "/kondisi-barang*",
                    "order" => 15,
                    "parent_id" => 0,
                    "created_at" => "2026-07-17 08:01:42",
                    "updated_at" => "2026-07-17 08:01:42"
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
                ],
                [
                    "id" => 2,
                    "name" => "Pemilik",
                    "slug" => "pemilik",
                    "created_at" => "2026-07-15 17:51:05",
                    "updated_at" => "2026-07-15 19:26:19"
                ],
                [
                    "id" => 3,
                    "name" => "Admin Operasional",
                    "slug" => "admin",
                    "created_at" => "2026-07-15 17:51:27",
                    "updated_at" => "2026-07-15 17:51:27"
                ],
                [
                    "id" => 4,
                    "name" => "Pegawai",
                    "slug" => "pegawai",
                    "created_at" => "2026-07-15 17:51:54",
                    "updated_at" => "2026-07-15 17:51:54"
                ]
            ]
        );

        Models\Setting::truncate();
		Models\Setting::insert(
			[

            ]
		);

		Models\Extension::truncate();
		Models\Extension::insert(
			[

            ]
		);

		Models\ExtensionHistory::truncate();
		Models\ExtensionHistory::insert(
			[

            ]
		);

        // pivot tables
        DB::table('admin_permission_menu')->truncate();
		DB::table('admin_permission_menu')->insert(
			[

            ]
		);

        DB::table('admin_role_menu')->truncate();
        DB::table('admin_role_menu')->insert(
            [
                [
                    "role_id" => 1,
                    "menu_id" => 1,
                    "created_at" => "2026-07-15 18:59:36",
                    "updated_at" => "2026-07-15 18:59:36"
                ],
                [
                    "role_id" => 1,
                    "menu_id" => 2,
                    "created_at" => "2026-07-15 18:59:48",
                    "updated_at" => "2026-07-15 18:59:48"
                ],
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
                    "menu_id" => 12,
                    "created_at" => "2026-07-15 18:31:47",
                    "updated_at" => "2026-07-15 18:31:47"
                ],
                [
                    "role_id" => 1,
                    "menu_id" => 13,
                    "created_at" => "2026-07-15 18:31:27",
                    "updated_at" => "2026-07-15 18:31:27"
                ],
                [
                    "role_id" => 1,
                    "menu_id" => 14,
                    "created_at" => "2026-06-19 05:54:44",
                    "updated_at" => "2026-06-19 05:54:44"
                ],
                [
                    "role_id" => 1,
                    "menu_id" => 15,
                    "created_at" => "2026-07-15 18:34:23",
                    "updated_at" => "2026-07-15 18:34:23"
                ],
                [
                    "role_id" => 1,
                    "menu_id" => 16,
                    "created_at" => "2026-07-15 17:29:56",
                    "updated_at" => "2026-07-15 17:29:56"
                ],
                [
                    "role_id" => 1,
                    "menu_id" => 17,
                    "created_at" => "2026-07-16 15:40:27",
                    "updated_at" => "2026-07-16 15:40:27"
                ],
                [
                    "role_id" => 1,
                    "menu_id" => 18,
                    "created_at" => "2026-07-17 06:33:57",
                    "updated_at" => "2026-07-17 06:33:57"
                ],
                [
                    "role_id" => 2,
                    "menu_id" => 8,
                    "created_at" => "2026-07-15 18:28:00",
                    "updated_at" => "2026-07-15 18:28:00"
                ],
                [
                    "role_id" => 2,
                    "menu_id" => 9,
                    "created_at" => "2026-07-15 18:30:55",
                    "updated_at" => "2026-07-15 18:30:55"
                ],
                [
                    "role_id" => 2,
                    "menu_id" => 10,
                    "created_at" => "2026-07-15 18:31:14",
                    "updated_at" => "2026-07-15 18:31:14"
                ],
                [
                    "role_id" => 2,
                    "menu_id" => 11,
                    "created_at" => "2026-07-15 18:31:37",
                    "updated_at" => "2026-07-15 18:31:37"
                ],
                [
                    "role_id" => 2,
                    "menu_id" => 12,
                    "created_at" => "2026-07-15 18:31:47",
                    "updated_at" => "2026-07-15 18:31:47"
                ],
                [
                    "role_id" => 2,
                    "menu_id" => 13,
                    "created_at" => "2026-07-15 18:31:27",
                    "updated_at" => "2026-07-15 18:31:27"
                ],
                [
                    "role_id" => 2,
                    "menu_id" => 14,
                    "created_at" => "2026-07-15 18:31:59",
                    "updated_at" => "2026-07-15 18:31:59"
                ],
                [
                    "role_id" => 2,
                    "menu_id" => 15,
                    "created_at" => "2026-07-15 18:34:23",
                    "updated_at" => "2026-07-15 18:34:23"
                ],
                [
                    "role_id" => 2,
                    "menu_id" => 16,
                    "created_at" => "2026-07-15 18:32:30",
                    "updated_at" => "2026-07-15 18:32:30"
                ],
                [
                    "role_id" => 3,
                    "menu_id" => 10,
                    "created_at" => "2026-07-15 18:31:14",
                    "updated_at" => "2026-07-15 18:31:14"
                ],
                [
                    "role_id" => 3,
                    "menu_id" => 15,
                    "created_at" => "2026-07-15 18:34:23",
                    "updated_at" => "2026-07-15 18:34:23"
                ],
                [
                    "role_id" => 3,
                    "menu_id" => 17,
                    "created_at" => "2026-07-16 15:40:27",
                    "updated_at" => "2026-07-16 15:40:27"
                ],
                [
                    "role_id" => 4,
                    "menu_id" => 15,
                    "created_at" => "2026-07-15 18:34:23",
                    "updated_at" => "2026-07-15 18:34:23"
                ],
                [
                    "role_id" => 4,
                    "menu_id" => 17,
                    "created_at" => "2026-07-17 06:16:57",
                    "updated_at" => "2026-07-17 06:16:57"
                ],
                [
                    "role_id" => 4,
                    "menu_id" => 18,
                    "created_at" => "2026-07-17 06:33:57",
                    "updated_at" => "2026-07-17 06:33:57"
                ]
            ]
        );

        DB::table('admin_role_permissions')->truncate();
        DB::table('admin_role_permissions')->insert(
            [
                [
                    "role_id" => 2,
                    "permission_id" => 7,
                    "created_at" => "2026-07-15 18:00:06",
                    "updated_at" => "2026-07-15 18:00:06"
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 8,
                    "created_at" => "2026-07-15 18:00:06",
                    "updated_at" => "2026-07-15 18:00:06"
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 9,
                    "created_at" => "2026-07-15 18:00:06",
                    "updated_at" => "2026-07-15 18:00:06"
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 10,
                    "created_at" => "2026-07-15 18:00:06",
                    "updated_at" => "2026-07-15 18:00:06"
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 11,
                    "created_at" => "2026-07-15 18:00:06",
                    "updated_at" => "2026-07-15 18:00:06"
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 12,
                    "created_at" => "2026-07-15 18:00:06",
                    "updated_at" => "2026-07-15 18:00:06"
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 14,
                    "created_at" => "2026-07-15 18:00:06",
                    "updated_at" => "2026-07-15 18:00:06"
                ],
                [
                    "role_id" => 3,
                    "permission_id" => 9,
                    "created_at" => "2026-07-15 18:00:42",
                    "updated_at" => "2026-07-15 18:00:42"
                ],
                [
                    "role_id" => 3,
                    "permission_id" => 10,
                    "created_at" => "2026-07-15 18:00:42",
                    "updated_at" => "2026-07-15 18:00:42"
                ],
                [
                    "role_id" => 3,
                    "permission_id" => 13,
                    "created_at" => "2026-07-15 18:00:42",
                    "updated_at" => "2026-07-15 18:00:42"
                ],
                [
                    "role_id" => 4,
                    "permission_id" => 10,
                    "created_at" => "2026-07-15 18:01:01",
                    "updated_at" => "2026-07-15 18:01:01"
                ],
                [
                    "role_id" => 4,
                    "permission_id" => 13,
                    "created_at" => "2026-07-15 18:01:01",
                    "updated_at" => "2026-07-15 18:01:01"
                ],
                [
                    "role_id" => 4,
                    "permission_id" => 15,
                    "created_at" => "2026-07-17 08:02:23",
                    "updated_at" => "2026-07-17 08:02:23"
                ]
            ]
        );

        // finish
    }
}
