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

            $table->unsignedBigInteger(
                'detail_penyewaan_barang_id'
            );

            $table->enum(
                'kondisi_sebelum',
                [
                    'baik',
                    'rusak',
                    'hilang'
                ]
            )->nullable();

            $table->enum(
                'kondisi_sesudah',
                [
                    'baik',
                    'rusak',
                    'hilang'
                ]
            )->nullable();
            $table->unsignedInteger('jumlah_bermasalah')
                ->default(0);

            $table->string(
                'catatan',
                100
            )->nullable();

            $table->timestamps();

            $table->foreign(
                'detail_penyewaan_barang_id'
            )
                ->references('id')
                ->on('detail_penyewaan_barang')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kondisi_barang');
    }
};
