<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('address')->nullable()->after('phone');
            $table->string('experience')->nullable()->after('address');
            $table->string('id_card_image')->nullable()->after('experience');
            $table->string('photo')->nullable()->after('id_card_image');
            $table->string('line_display_name')->nullable()->after('photo');
            $table->string('line_picture_url')->nullable()->after('line_display_name');
            $table->boolean('pdpa_accepted')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'experience',
                'id_card_image',
                'photo',
                'line_display_name',
                'line_picture_url',
                'pdpa_accepted',
            ]);
        });
    }
};
