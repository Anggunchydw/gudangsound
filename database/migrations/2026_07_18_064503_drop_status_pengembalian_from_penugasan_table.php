<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('penugasan', function (Blueprint $table) {
            $table->dropColumn('status_pengembalian');
        });
    }

    public function down()
    {
        Schema::table('penugasan', function (Blueprint $table) {
            $table->enum('status_pengembalian', [
                'belum_dikembalikan',
                'sudah_dikembalikan'
            ])->default('belum_dikembalikan');
        });
    }
};
