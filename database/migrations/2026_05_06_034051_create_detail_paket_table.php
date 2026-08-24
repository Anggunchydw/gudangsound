<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_paket', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paket_id');
            $table->unsignedBigInteger('barang_id');
            $table->integer('jumlah');
            $table->timestamps();

            // Constraint Composite Unique
            $table->unique(['paket_id', 'barang_id']);

            $table->foreign('paket_id')->references('id')->on('paket')->onDelete('cascade');
            $table->foreign('barang_id')->references('id')->on('barang')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_paket');
    }
};
