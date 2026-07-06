<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_penyewaan_barang', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('penyewaan_id');

            $table->unsignedBigInteger('barang_id');

            $table->integer('jumlah_barang');

            $table->timestamps();

            $table->foreign('penyewaan_id')
                ->references('id')
                ->on('penyewaan')
                ->onDelete('cascade');

            $table->foreign('barang_id')
                ->references('id')
                ->on('barang')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penyewaan_barang');
    }
};
