<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Applicant;

echo "--- ALL APPLICANTS ---\n";
$applicants = Applicant::orderBy('id', 'desc')->take(20)->get();
foreach ($applicants as $a) {
    echo "ID: {$a->id} | Name: {$a->name} | Pos: '{$a->position}' | LINE: {$a->line_user_id} | Update: {$a->updated_at}\n";
}
