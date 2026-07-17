<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kondisi_barang', function (Blueprint $table) {

            // hapus foreign lama
            $table->dropForeign(['detail_penyewaan_barang_id']);

            // hapus kolom lama
            $table->dropColumn('detail_penyewaan_barang_id');

            // relasi baru
            $table->unsignedBigInteger('penugasan_id')->after('id');

            $table->unsignedBigInteger('barang_id')->after('penugasan_id');

            // jumlah barang yang dibawa
            $table->unsignedInteger('jumlah_barang')
                ->default(1)
                ->after('barang_id');

            // foreign key baru
            $table->foreign('penugasan_id')
                ->references('id')
                ->on('penugasan')
                ->cascadeOnDelete();

            $table->foreign('barang_id')
                ->references('id')
                ->on('barang')
                ->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::table('kondisi_barang', function (Blueprint $table) {

            $table->dropForeign(['penugasan_id']);
            $table->dropForeign(['barang_id']);

            $table->dropColumn([
                'penugasan_id',
                'barang_id',
                'jumlah_barang'
            ]);

            $table->unsignedBigInteger('detail_penyewaan_barang_id');

            $table->foreign('detail_penyewaan_barang_id')
                ->references('id')
                ->on('detail_penyewaan_barang')
                ->cascadeOnDelete();
        });
    }
};
