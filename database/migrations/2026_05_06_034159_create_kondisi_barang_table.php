<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kondisi_barang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('detail_penyewaan_id');
            $table->enum('kondisi_sebelum', ['baik','rusak','hilang']);
            $table->enum('kondisi_sesudah', ['baik','rusak','hilang']);
            $table->string('catatan', 100)->nullable();
            $table->timestamps();

             $table->foreign('detail_penyewaan_id')
            ->references('id')
            ->on('detail_penyewaan')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kondisi_barang');
    }
};
