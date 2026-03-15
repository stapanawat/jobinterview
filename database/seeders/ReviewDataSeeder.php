<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Applicant;
use App\Models\Review;
use App\Models\Position;

class ReviewDataSeeder extends Seeder
{
    public function run(): void
    {
        // Clear old test data if needed, but here we just want to ADD
        
        $positions = ['พนักงานเสิร์ฟ', 'พนักงานชงน้ำ', 'พนักงานล้างจาน'];
        foreach ($positions as $pos) {
            Position::updateOrCreate(['name' => $pos], ['is_active' => true]);
        }

        $applicants = Applicant::all();
        echo "Found " . $applicants->count() . " applicants.\n";

        foreach ($applicants as $applicant) {
            echo "Processing Applicant ID: {$applicant->id}, Name: {$applicant->name}, Pos: {$applicant->position}\n";
            
            // 1. Give them 5 reviews as if they reviewed the shop
            for ($i = 0; $i < 5; $i++) {
                Review::create([
                    'applicant_id' => $applicant->id,
                    'reviewer_type' => 'employee',
                    'rating' => rand(3, 5),
                    'comment' => "รีวิวงานอัตโนมัติรายการที่ " . ($i + 1)
                ]);
            }

            // 2. Give them 5 reviews as if the shop reviewed them (Employee Scores)
            for ($i = 0; $i < 5; $i++) {
                Review::create([
                    'applicant_id' => $applicant->id,
                    'reviewer_type' => 'shop',
                    'rating' => rand(4, 5),
                    'comment' => "คะแนนพนักงานอัตโนมัติรายการที่ " . ($i + 1)
                ]);
            }
        }

        echo "Seeded reviews for all current applicants.\n";
    }
}
