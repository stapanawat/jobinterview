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
        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating_punctuality')->nullable()->after('comment');
            $table->unsignedTinyInteger('rating_showed_up')->nullable()->after('rating_punctuality');
            $table->unsignedTinyInteger('rating_honesty')->nullable()->after('rating_showed_up');
            $table->unsignedTinyInteger('rating_diligence')->nullable()->after('rating_honesty');
            $table->unsignedTinyInteger('rating_following_instructions')->nullable()->after('rating_diligence');
            $table->unsignedTinyInteger('rating_others')->nullable()->after('rating_following_instructions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn([
                'rating_punctuality',
                'rating_showed_up',
                'rating_honesty',
                'rating_diligence',
                'rating_following_instructions',
                'rating_others',
            ]);
        });
    }
};
