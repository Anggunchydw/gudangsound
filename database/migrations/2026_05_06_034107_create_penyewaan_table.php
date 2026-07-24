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
        Schema::create('penyewaan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_penyewa', 100);
            $table->string('no_tlp', 20);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('lokasi', 100);
            $table->text('keterangan')->nullable();
            $table->bigInteger('total_harga');
            $table->bigInteger('uang_muka')->default(0);
            $table->enum('status_pembayaran', ['DP', 'Lunas']);
            $table->enum('status_penyewaan', [
                'booking',
                'berlangsung',
                'selesai',
                'dibatalkan'
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyewaan');
    }
};
