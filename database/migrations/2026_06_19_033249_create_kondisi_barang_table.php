<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kondisi_barang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penugasan_id');
            $table->unsignedBigInteger('barang_id');

            $table->unsignedInteger('jumlah_barang')->default(1);
            $table->enum('kondisi_sebelum', ['baik', 'rusak', 'hilang'])->nullable();
            $table->enum('kondisi_sesudah', ['baik', 'rusak', 'hilang'])->nullable();
            $table->unsignedInteger('jumlah_bermasalah')->default(0);
            $table->string('catatan', 1000)->nullable();

            $table->timestamps();

            // Composite Unique Constraint (Mencegah 1 barang diinput dobel dalam 1 penugasan)
            $table->unique(['penugasan_id', 'barang_id']);

            $table->foreign('penugasan_id')
                ->references('id')
                ->on('penugasan')
                ->onDelete('cascade');

            $table->foreign('barang_id')
                ->references('id')
                ->on('barang')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kondisi_barang');
    }
};
