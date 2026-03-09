<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Applicant;
use App\Models\Interview;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1 — รอตรวจสอบ
        Applicant::create([
            'line_user_id' => 'test_user_01',
            'name' => 'สมชาย ใจดี',
            'phone' => '081-111-1111',
            'position' => 'พนักงานเสิร์ฟ',
            'status' => 'pending_review',
            'age' => 25,
            'current_residence' => 'กรุงเทพฯ',
            'education_level' => 'ปริญญาตรี',
            'experience' => 'เสิร์ฟร้านอาหาร 2 ปี',
        ]);

        // 2 — นัดสัมภาษณ์แล้ว
        $a2 = Applicant::create([
            'line_user_id' => 'test_user_02',
            'name' => 'สมหญิง รักงาน',
            'phone' => '082-222-2222',
            'position' => 'พนักงานชงน้ำ',
            'status' => 'scheduled',
            'age' => 22,
            'current_residence' => 'นนทบุรี',
            'education_level' => 'ม.6',
            'experience' => 'ชงกาแฟ 1 ปี',
        ]);
        Interview::create([
            'applicant_id' => $a2->id,
            'interview_date' => '2026-03-12',
            'interview_time' => '10:00',
            'location' => 'สาขาราชเทวี',
            'status' => 'scheduled',
        ]);

        // 3 — ยืนยันเวลานัด
        $a3 = Applicant::create([
            'line_user_id' => 'test_user_03',
            'name' => 'วิชัย มานะ',
            'phone' => '083-333-3333',
            'position' => 'พนักงานล้างจาน',
            'status' => 'time_confirmed',
            'age' => 30,
            'current_residence' => 'ปทุมธานี',
            'education_level' => 'ม.3',
        ]);
        Interview::create([
            'applicant_id' => $a3->id,
            'interview_date' => '2026-03-11',
            'interview_time' => '14:00',
            'location' => 'สาขาพญาไท',
            'status' => 'confirmed',
        ]);

        // 4 — ยืนยันเข้าร่วม
        $a4 = Applicant::create([
            'line_user_id' => 'test_user_04',
            'name' => 'พรทิพย์ สวยงาม',
            'phone' => '084-444-4444',
            'position' => 'พนักงานเสิร์ฟ',
            'status' => 'attendance_confirmed',
            'age' => 28,
            'current_residence' => 'กรุงเทพฯ',
            'education_level' => 'ปวส.',
        ]);
        Interview::create([
            'applicant_id' => $a4->id,
            'interview_date' => '2026-03-10',
            'interview_time' => '09:30',
            'location' => 'สาขาอารีย์',
            'status' => 'confirmed',
            'day_before_confirmed' => true,
            'day_before_reminder_sent' => true,
        ]);

        // 5 — กำลังทำงาน
        Applicant::create([
            'line_user_id' => 'test_user_05',
            'name' => 'ธนวัฒน์ เก่งกาจ',
            'phone' => '085-555-5555',
            'position' => 'พนักงานชงน้ำ',
            'status' => 'working',
            'age' => 27,
            'current_residence' => 'กรุงเทพฯ',
            'education_level' => 'ปริญญาตรี',
            'job_description' => 'ชงกาแฟ สาขาราชเทวี',
        ]);

        // 6 — กำลังทำงาน
        Applicant::create([
            'line_user_id' => 'test_user_06',
            'name' => 'นภาพร ขยัน',
            'phone' => '086-666-6666',
            'position' => 'พนักงานเสิร์ฟ',
            'status' => 'working',
            'age' => 24,
            'current_residence' => 'กรุงเทพฯ',
            'education_level' => 'ม.6',
            'job_description' => 'เสิร์ฟอาหาร สาขาพญาไท',
        ]);

        // 7 — เลิกจ้าง
        Applicant::create([
            'line_user_id' => 'test_user_07',
            'name' => 'ประเสริฐ หมดใจ',
            'phone' => '087-777-7777',
            'position' => 'พนักงานล้างจาน',
            'status' => 'terminated',
            'age' => 35,
            'current_residence' => 'สมุทรปราการ',
            'education_level' => 'ม.3',
            'job_description' => 'ล้างจาน สาขาราชเทวี',
        ]);

        echo "Created 7 test applicants + 3 interviews!\n";
    }
}
