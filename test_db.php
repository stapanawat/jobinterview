<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$interview = App\Models\Interview::find(1);
if ($interview) {
    echo "Before: Interview status = " . $interview->status . ", Applicant status = " . $interview->applicant->status . "\n";
    $interview->update(['status' => 'time_confirmed']);
    $interview->applicant->update(['status' => 'time_confirmed']);
    $interview->refresh();
    echo "After: Interview status = " . $interview->status . ", Applicant status = " . $interview->applicant->status . "\n";
} else {
    echo "Interview not found\n";
}
