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
        Schema::create('penugasan', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('penyewaan_id');

            $table->string('tim')->nullable();

            $table->timestamps();

            $table->foreign('penyewaan_id')
                ->references('id')
                ->on('penyewaan')
                ->onDelete('cascade');
            $table->string('google_event_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penugasan');
    }
};
