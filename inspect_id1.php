<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Applicant;
use App\Models\Review;

$id = 1;
$a = Applicant::find($id);
echo "--- APPLICANT ID {$id} ---\n";
echo "Name: {$a->name}\n";
echo "Position: '{$a->position}'\n";
echo "LINE ID: '{$a->line_user_id}'\n";
echo "Updated: {$a->updated_at}\n";

echo "\n--- REVIEWS FOR ID {$id} ---\n";
$reviews = Review::where('applicant_id', $id)->orderBy('id', 'desc')->get();
foreach ($reviews as $r) {
    echo "ID: {$r->id} | Rating: {$r->rating} | Created: {$r->created_at}\n";
}

echo "\n--- SEARCHING FOR OTHER 'Stapanawat' APPLICANTS ---\n";
$others = Applicant::where('name', 'like', '%Stapanawat%')->where('id', '!=', $id)->get();
foreach ($others as $o) {
    echo "ID: {$o->id} | Name: {$o->name} | Pos: '{$o->position}' | LINE: '{$o->line_user_id}' | Updated: {$o->updated_at}\n";
}
