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
        Schema::create('pemasukan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penyewaan_id');

            $table->date('tanggal_masuk');
            $table->bigInteger('jumlah');
            $table->enum('jenis_pembayaran', ['DP', 'Lunas']);
            $table->string('keterangan', 100)->nullable();
            $table->timestamps();

            $table->foreign('penyewaan_id')
                ->references('id')
                ->on('penyewaan')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemasukan');
    }
};
