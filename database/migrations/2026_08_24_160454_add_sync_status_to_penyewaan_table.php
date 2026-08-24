<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penyewaan', function (Blueprint $table) {
            $table->string('calendar_sync_status')
                ->default('pending')
                ->after('google_event_id');

            $table->string('notification_status')
                ->default('pending')
                ->after('calendar_sync_status');
        });
    }

    public function down(): void
    {
        Schema::table('penyewaan', function (Blueprint $table) {
            $table->dropColumn(['calendar_sync_status', 'notification_status']);
        });
    }
};
