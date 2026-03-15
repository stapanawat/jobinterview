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
        Schema::create('applicant_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained('applicants')->onDelete('cascade');
            $table->string('line_user_id');
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('position')->nullable();
            $table->string('experience')->nullable();
            $table->string('id_card_image')->nullable();
            $table->string('photo')->nullable();
            $table->string('line_display_name')->nullable();
            $table->string('line_picture_url')->nullable();
            $table->string('status')->nullable();
            $table->boolean('pdpa_accepted')->default(false);
            $table->string('current_residence')->nullable();
            $table->string('current_occupation')->nullable();
            $table->integer('age')->nullable();
            $table->string('education_level')->nullable();
            $table->integer('number_of_children')->nullable();
            $table->string('can_drive_motorcycle')->nullable();
            $table->text('pros_and_cons')->nullable();
            $table->text('life_dream')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('preferred_working_hours')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_histories');
    }
};
