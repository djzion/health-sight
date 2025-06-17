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
use App\Http\Middleware\CheckQualityImprovementAccess;
use Illuminate\Support\Facades\Route;

// Root route
Route::get('/', function () {
    return view('auth.login');
});

// Public API routes for location data (used by both QIP and Registration)
Route::prefix('api')->group(function () {
    Route::get('/districts/{district}/lgas', function ($districtId) {
        return App\Models\Lga::where('district_id', $districtId)
            ->orderBy('name')
            ->get(['id', 'name']);
    });

    Route::get('/lgas/{lga}/phcs', function ($lgaId) {
        return App\Models\Phc::where('lga_id', $lgaId)
            ->orderBy('name')
            ->get(['id', 'name']);
    });
});

// Legacy routes for backwards compatibility (choose one controller for each)
Route::get('/get-lgas/{districtId}', [QipController::class, 'getLgas']);
Route::get('/get-phcs/{lgaId}', [QipController::class, 'getPhcs']);

// Authenticated routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Password change routes
    Route::get('/change-password', [ChangePasswordController::class, 'show'])->name('change-password.show');
    Route::post('/change-password', [ChangePasswordController::class, 'update'])->name('change-password.update');

    // Basic user/role/PHC management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/phcs', [PhcController::class, 'index'])->name('phcs.index');
    Route::post('/phcs', [PhcController::class, 'store'])->name('phcs.store');

    // Assessment routes
    Route::prefix('assessments')->name('assessments.')->group(function () {
        Route::get('/', [AssessmentController::class, 'index'])->name('index');
        Route::post('/', [AssessmentController::class, 'store'])->name('store');
        Route::put('/', [AssessmentController::class, 'update'])->name('update');
        Route::get('/no-available-assessments', [AssessmentController::class, 'noAvailableAssessments'])->name('no-available-assessments');
        Route::get('/dashboard', [AssessmentController::class, 'dashboard'])->name('dashboard')->middleware('approved');
        Route::get('/select-location', [AssessmentController::class, 'selectLocationForm'])->name('select-location');
        Route::post('/select-location', [AssessmentController::class, 'processLocationSelection'])->name('process-location');
        Route::post('/select-phc', [AssessmentController::class, 'selectPHC'])->name('select-phc');
        Route::post('/reset-location', [AssessmentController::class, 'resetLocation'])->name('reset-location');
        Route::get('/show', [AssessmentController::class, 'showAssessment'])->name('show');
        Route::post('/save-temporary', [AssessmentController::class, 'saveTemporary'])->name('save-temporary');
        Route::get('/load-temporary/{phcId}', [AssessmentController::class, 'loadTemporary'])->name('load-temporary');
        Route::get('/{assessment}/child-questions', [AssessmentController::class, 'getChildQuestions']);
    });

    // Assessment submission
    Route::post('/assessments/submit', [UserDashboardController::class, 'submit'])->name('submit.assessment');

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminController::class, 'index'])->name('users');
        Route::get('/users/create', [AdminController::class, 'create'])->name('users.create');
        Route::post('/users/store', [AdminController::class, 'store'])->name('users.store');
        Route::post('/users/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('users.reset-password');
        Route::get('/pending-users', [AdminController::class, 'pendingUsers'])->name('pendingusers');
        Route::post('/users/{user}/approve', [AdminController::class, 'approve'])->name('users.approve');
        Route::post('/users/{user}/reject', [AdminController::class, 'reject'])->name('users.reject');

        // Assessment settings
        // FIXED:
        Route::get('/set-next-date', [AdminAssessmentController::class, 'showSettingsForm'])->name('assessments.set-next-date-form');
        Route::post('/set-next-date', [AdminAssessmentController::class, 'setNextDate'])->name('assessments.set-next-date');

        Route::prefix('assessment-periods')->name('assessment-periods.')->group(function () {
            Route::get('/', [AdminAssessmentController::class, 'index'])->name('index');
            Route::get('/list', [AdminAssessmentController::class, 'getPeriods'])->name('list');
            Route::post('/', [AdminAssessmentController::class, 'createPeriod'])->name('create');
            Route::patch('/{period}/toggle-status', [AdminAssessmentController::class, 'togglePeriodStatus'])->name('toggle');
            Route::delete('/{period}', [AdminAssessmentController::class, 'deletePeriod'])->name('delete');
            Route::get('/statistics', [AdminAssessmentController::class, 'getStatistics'])->name('statistics');
        });

        // Assessment Periods Management (Admin only)
        Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
            Route::prefix('assessment-periods')->name('assessment-periods.')->group(function () {
                Route::get('/', [AdminAssessmentController::class, 'index'])->name('index');
                Route::get('/list', [AdminAssessmentController::class, 'getPeriods'])->name('list');
                Route::post('/', [AdminAssessmentController::class, 'createPeriod'])->name('create');
                Route::patch('/{period}/toggle-status', [AdminAssessmentController::class, 'togglePeriodStatus'])->name('toggle');
                Route::delete('/{period}', [AdminAssessmentController::class, 'deletePeriod'])->name('delete');
                Route::get('/statistics', [AdminAssessmentController::class, 'getStatistics'])->name('statistics');
            });
        });

        // SafeCare period management
        Route::prefix('safecare')->name('safecare.')->group(function () {
            Route::get('/dashboard', function () {
                return view('admin.safecare.dashboard');
            })->name('dashboard');
            Route::post('/periods', [QipController::class, 'createAssessmentPeriod'])->name('periods.create');
            Route::get('/periods', [QipController::class, 'getAssessmentPeriods'])->name('periods.index');
            Route::patch('/periods/{period}/toggle-status', [QipController::class, 'togglePeriodStatus'])->name('periods.toggle');
        });
    });
});

// Quality Improvement routes (with proper middleware)
Route::middleware(['auth', CheckQualityImprovementAccess::class])->prefix('qip')->name('qip.')->group(function () {
    Route::get('/', [QipController::class, 'index'])->name('index');
    Route::post('/', [QipController::class, 'store'])->name('store');
    Route::get('/no-available-assessments', [QipController::class, 'noAvailableAssessments'])->name('no-available-assessments');
});

// SafeCare Assessment routes (with proper middleware and CSRF protection)
Route::middleware(['auth', CheckQualityImprovementAccess::class])->group(function () {
    // GET routes
    Route::get('/get-safecare-assessment', [QipController::class, 'getSafecareAssessment'])->name('safecare.get');
    Route::get('/safecare-assessment-history', [QipController::class, 'getAssessmentHistory'])->name('safecare.history');
    Route::get('/safecare-analytics', [QipController::class, 'getAnalytics'])->name('safecare.analytics');

    // POST routes (with CSRF protection)
    Route::post('/save-safecare-assessment', [QipController::class, 'saveSafecareAssessment'])->name('safecare.save');
    Route::post('/update-safecare-assessment', [QipController::class, 'updateSafecareAssessment'])->name('safecare.update');
    Route::post('/compare-safecare-assessments', [QipController::class, 'compareAssessments'])->name('safecare.compare');
});

// Logout route
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Debug routes (remove in production)
Route::get('/debug-role-access', [AssessmentController::class, 'debugRoleAccess']);
Route::get('/assessment-debug', function () {
    $assessments = \App\Models\Assessment::whereNull('parent_id')
        ->orderBy('id')
        ->select('id', 'question', 'order')
        ->get();
    return view('debug-assessments', compact('assessments'));
});

// Test route for SafeCare (remove in production)
Route::get('/test-actual-safecare', function () {
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'district_id' => 1,
        'lga_id' => 2,
        'phc_id' => 6
    ]);
    $controller = new \App\Http\Controllers\QipController();
    return $controller->getSafecareAssessment($request);
});

// Permission-based route
Route::get('/admin/users', [RegisteredUserController::class, 'index'])
    ->middleware('permission:view-users');


Route::get('/admin/safecare-periods', [QipController::class, 'getAssessmentPeriods'])
    ->name('admin.safecare.periods.index');

Route::patch('/admin/safecare-periods/{period}/toggle-status', [QipController::class, 'togglePeriodStatus'])
    ->name('admin.safecare.periods.toggle');

// Include authentication routes
require __DIR__ . '/auth.php';
