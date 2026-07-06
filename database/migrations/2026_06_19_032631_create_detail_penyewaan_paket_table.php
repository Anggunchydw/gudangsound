<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_penyewaan_paket', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('penyewaan_id');

            $table->unsignedBigInteger('paket_id');

            $table->integer('jumlah_paket')
                ->default(1);

            $table->timestamps();

            $table->foreign('penyewaan_id')
                ->references('id')
                ->on('penyewaan')
                ->onDelete('cascade');

            $table->foreign('paket_id')
                ->references('id')
                ->on('paket')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penyewaan_paket');
    }
};
