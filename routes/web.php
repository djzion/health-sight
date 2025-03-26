<?php

use App\Http\Controllers\AdminAssessmentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PhcController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QipController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Middleware\CheckQualityImprovementOfficer;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/assessments/submit', [UserDashboardController::class, 'submit'])->name('submit.assessment');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
Route::get('/phcs', [PhcController::class, 'index'])->name('phcs.index');
Route::post('/phcs', [PhcController::class, 'store'])->name('phcs.store');

Route::get('/assessments/{assessment}/child-questions', [AssessmentController::class, 'getChildQuestions']);



Route::middleware(['auth'])->group(function () {
    Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
    Route::post('/assessments', [AssessmentController::class, 'store'])->name('assessments.store');
    Route::put('/assessments', [AssessmentController::class, 'update']);
    Route::get('/no-available-assessments', [AssessmentController::class, 'noAvailableAssessments'])->name('no-available-assessments');
});

Route::post('/assessments/select-phc', [App\Http\Controllers\AssessmentController::class, 'selectPHC'])->name('assessments.select-phc');

Route::get('/assessments/dashboard', [AssessmentController::class, 'dashboard'])
    ->name('assessments.dashboard')
    ->middleware(['auth', 'approved']);


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', CheckQualityImprovementOfficer::class])->group(function () {
    Route::get('/qip', [QipController::class, 'index'])->name('qip.index');
    Route::post('/qip', [QipController::class, 'store'])->name('qip.store');
});

Route::get('/get-safecare-assessment', [QipController::class, 'getSafecareAssessment']);
Route::post('/save-safecare-assessment', [QipController::class, 'saveSafecareAssessment'])->name('safecare.save');

Route::get('/debug-role-access', [AssessmentController::class, 'debugRoleAccess']);

Route::middleware(['auth'])->group(function () {
    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminController::class, 'index'])->name('users');
        Route::get('/users/create', [AdminController::class, 'create'])->name('users.create');
        Route::post('/users/store', [AdminController::class, 'store'])->name('users.store');
        Route::post('/users/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('users.reset-password');
        Route::get('/pending-users', [AdminController::class, 'pendingUsers'])->name('pendingusers');
        Route::post('/users/{user}/approve', [AdminController::class, 'approve'])->name('users.approve');
        Route::post('/users/{user}/reject', [AdminController::class, 'reject'])->name('users.reject');

        // Assessment settings routes
        Route::get('/set-next-date', function () {
            return view('admin.assessments.settings');
        })->name('assessments.set-next-date-form');
        Route::post('/set-next-date', [AdminAssessmentController::class, 'setNextDate'])
            ->name('assessments.set-next-date');
    });

    // Password change routes
    Route::get('/change-password', [ChangePasswordController::class, 'show'])->name('change-password.show');
    Route::post('/change-password', [ChangePasswordController::class, 'update'])->name('change-password.update');
});


Route::get('/api/districts/{district}/lgas', function ($districtId) {
    return App\Models\Lga::where('district_id', $districtId)
        ->orderBy('name')
        ->get(['id', 'name']);
});

Route::get('/api/lgas/{lga}/phcs', function ($lgaId) {
    return App\Models\Phc::where('lga_id', $lgaId)
        ->orderBy('name')
        ->get(['id', 'name']);
});

Route::get('/assessments/show', [App\Http\Controllers\AssessmentController::class, 'showAssessment'])->name('assessments.show');

// In routes/web.php
Route::get('/assessments/select-location', [AssessmentController::class, 'selectLocationForm'])->name('assessments.select-location');
Route::post('/assessments/select-location', [AssessmentController::class, 'processLocationSelection'])->name('assessments.process-location');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});


Route::post('/assessments/reset-location', [App\Http\Controllers\AssessmentController::class, 'resetLocation'])->name('assessments.reset-location');

Route::get('/get-lgas/{districtId}', [RegisteredUserController::class, 'getLgas']);
Route::get('/get-phcs/{lgaId}', [RegisteredUserController::class, 'getPhcs']);


Route::get('/get-lgas/{districtId}', [QipController::class, 'getLgas']);
Route::get('/get-phcs/{lgaId}', [QipController::class, 'getPhcs']);


Route::get('/admin/users', [RegisteredUserController::class, 'index'])
    ->middleware('permission:view-users');




require __DIR__ . '/auth.php';


// Create a temporary route to see all questions
Route::get('/assessment-debug', function () {
    $assessments = \App\Models\Assessment::whereNull('parent_id')
        ->orderBy('id')
        ->select('id', 'question', 'order')
        ->get();

    return view('debug-assessments', compact('assessments'));
});

Route::post('/assessments/save-temporary', [AssessmentController::class, 'saveTemporary'])
    ->name('assessments.save-temporary')
    ->middleware(['auth']);

Route::get('/assessments/load-temporary/{phcId}', [AssessmentController::class, 'loadTemporary'])
    ->name('assessments.load-temporary')
    ->middleware(['auth']);
