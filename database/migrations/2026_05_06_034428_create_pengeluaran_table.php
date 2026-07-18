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
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penyewaan_id')->nullable();

            $table->bigInteger('jumlah_pengeluaran');
            $table->date('tanggal_pengeluaran');
            $table->enum('kategori', ['transport', 'perbaikan', 'gaji', 'operasional', 'lainnya']);
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();

            $table->foreign('penyewaan_id')
                ->references('id')
                ->on('penyewaan')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran');
    }
};
