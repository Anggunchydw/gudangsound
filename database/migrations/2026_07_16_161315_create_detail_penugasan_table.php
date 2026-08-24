<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_penugasan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penugasan_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            // Constraint Composite Unique
            $table->unique(['penugasan_id', 'user_id']);

            $table->foreign('penugasan_id')
                ->references('id')
                ->on('penugasan')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('admin_users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penugasan');
    }
};
