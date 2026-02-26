<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->boolean('day_before_confirmed')->default(false)->after('reminder_sent');
            $table->boolean('day_before_reminder_sent')->default(false)->after('day_before_confirmed');
        });
    }

    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->dropColumn(['day_before_confirmed', 'day_before_reminder_sent']);
        });
    }
};
