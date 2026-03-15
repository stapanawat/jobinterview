<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Applicant;

echo "--- SEARCHING BY NAME AND POSITION ---\n";
$apps = Applicant::where('name', 'like', '%Stapanawat%')->get();
foreach ($apps as $a) {
    echo "ID: {$a->id} | Name: {$a->name} | Pos: '{$a->position}' | LINE: '{$a->line_user_id}' | Updated: {$a->updated_at}\n";
}

echo "\n--- LATEST APPLICANTS IN WHOLE DB ---\n";
$latest = Applicant::orderBy('id', 'desc')->take(10)->get();
foreach ($latest as $l) {
    echo "ID: {$l->id} | Name: {$l->name} | Pos: '{$l->position}' | LINE: '{$l->line_user_id}'\n";
}
