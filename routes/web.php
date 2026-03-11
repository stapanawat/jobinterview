<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LineController;
use App\Http\Controllers\PublicApplicationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::post('/webhook/line', [LineController::class, 'handle']);

// Minimal Health Check for Render
Route::get('/health', function () {
    return response('OK', 200)->header('Content-Type', 'text/plain');
});

// External Cron Jobs (For Render/Free Hosts)
Route::get('/cron/remind-tomorrow', function () {
    \Illuminate\Support\Facades\Artisan::call('interviews:remind-day-before');
    return response()->json([
        'status' => 'success',
        'message' => 'Day before reminders checked'
    ]);
});

Route::get('/cron/remind-immediate', function () {
    \Illuminate\Support\Facades\Artisan::call('interviews:remind-immediate');
    return response()->json(['status' => 'success', 'message' => 'Immediate reminders sent (or checked)']);
});

// Public application form (LIFF)
Route::get('/apply', [PublicApplicationController::class, 'showForm'])->name('apply.form');
Route::post('/apply', [PublicApplicationController::class, 'submitForm'])->name('apply.submit');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/updates', [DashboardController::class, 'updates'])->name('dashboard.updates');
    Route::get('/employees/export', [DashboardController::class, 'exportEmployees'])->name('employees.export');
    Route::get('/interviews/create', [App\Http\Controllers\InterviewController::class, 'create'])->name('interviews.create');
    Route::post('/interviews', [App\Http\Controllers\InterviewController::class, 'store'])->name('interviews.store');
    Route::post('/interviews/{applicant}/cancel', [App\Http\Controllers\InterviewController::class, 'cancel'])->name('interviews.cancel');

    Route::get('/reviews', [App\Http\Controllers\ReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/create', [App\Http\Controllers\ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

    Route::post('/positions', [App\Http\Controllers\PositionController::class, 'store'])->name('positions.store');
    Route::put('/positions/{position}', [App\Http\Controllers\PositionController::class, 'update'])->name('positions.update');
    Route::delete('/positions/{position}', [App\Http\Controllers\PositionController::class, 'destroy'])->name('positions.destroy');

    Route::patch('/applicants/{applicant}/status', [DashboardController::class, 'updateStatus'])->name('applicants.updateStatus');
    Route::patch('/applicants/{applicant}/notes', [DashboardController::class, 'updateNotes'])->name('applicants.updateNotes');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
