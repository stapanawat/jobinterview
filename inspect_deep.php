<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Applicant;
use App\Models\Review;

echo "--- DUMMY IDS ---\n";
$dummies = Applicant::where('line_user_id', 'like', 'dummy%')->get();
foreach ($dummies as $d) {
    echo "ID: {$d->id} | Name: {$d->name} | Pos: '{$d->position}' | Update: {$d->updated_at}\n";
}

echo "\n--- CHECKING REVIEW 530 AGAIN ---\n";
$r = Review::find(530);
if ($r) {
    echo "Review 530: Rating {$r->rating} | AppID {$r->applicant_id} | Created {$r->created_at}\n";
    $a = $r->applicant;
    if ($a) {
        echo "Linked Applicant: ID {$a->id} | Name {$a->name} | Pos '{$a->position}'\n";
    } else {
        echo "No Applicant linked to Review 530\n";
    }
} else {
    echo "Review 530 not found!\n";
}

echo "\n--- APPLICANTS WITH POSITION 'พนักงานเสิร์ฟ' ---\n";
$pps = Applicant::where('position', 'like', '%พนักงานเสิร์ฟ%')->get();
foreach ($pps as $p) {
    echo "ID: {$p->id} | Name: {$p->name} | LINE: {$p->line_user_id} | Update: {$p->updated_at}\n";
}
