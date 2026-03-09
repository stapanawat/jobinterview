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
            $table->string('current_residence')->nullable()->comment('ที่พักปัจจุบัน');
            $table->string('current_occupation')->nullable()->comment('ปัจจุบันทำอะไร');
            $table->integer('age')->nullable()->comment('อายุ');
            $table->string('education_level')->nullable()->comment('จบการศึกษา');
            $table->integer('number_of_children')->nullable()->comment('มีบุตรกี่คน');
            $table->string('can_drive_motorcycle')->nullable()->comment('ขับขี่รถจักรยานยนต์');
            $table->text('pros_and_cons')->nullable()->comment('ข้อดีข้อเสีย');
            $table->text('life_dream')->nullable()->comment('ความฝันในชีวิต');
            $table->string('emergency_contact')->nullable()->comment('บุคคลติดต่อฉุกเฉิน');
            $table->string('preferred_working_hours')->nullable()->comment('เวลาทำงานและวันหยุด');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn([
                'current_residence',
                'current_occupation',
                'age',
                'education_level',
                'number_of_children',
                'can_drive_motorcycle',
                'pros_and_cons',
                'life_dream',
                'emergency_contact',
                'preferred_working_hours',
            ]);
        });
    }
};
