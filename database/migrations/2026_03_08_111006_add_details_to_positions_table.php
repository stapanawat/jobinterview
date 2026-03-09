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
        Schema::table('positions', function (Blueprint $table) {
            $table->string('shop_name')->nullable()->comment('ชื่อร้าน');
            $table->text('location')->nullable()->comment('ที่ตั้ง');
            $table->text('description')->nullable()->comment('รายละเอียด');
            $table->text('duties')->nullable()->comment('หน้าที่');
            $table->string('salary')->nullable()->comment('เงินเดือน');
            $table->string('extra_pay')->nullable()->comment('เงินพิเศษ');
            $table->string('working_hours')->nullable()->comment('เวลาทำงาน');
            $table->string('days_off')->nullable()->comment('วันหยุด');
            $table->text('benefits')->nullable()->comment('สวัสดิการอื่นๆ');
            $table->text('qualifications')->nullable()->comment('คุณสมบัติผู้สมัคร');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn([
                'shop_name',
                'location',
                'description',
                'duties',
                'salary',
                'extra_pay',
                'working_hours',
                'days_off',
                'benefits',
                'qualifications'
            ]);
        });
    }
};
