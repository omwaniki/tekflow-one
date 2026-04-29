<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AssetStatusController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\AssetAssignmentController;
use App\Http\Controllers\AssetMovementController;
use App\Http\Controllers\SettingsController;

/*
|--------------------------------------------------------------------------
| Public Landing Page
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Tekflow One Inventory Modules
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | 🔥 ASSETS CUSTOM ROUTES (MUST COME BEFORE RESOURCE)
    |--------------------------------------------------------------------------
    */

    Route::post('/assets/import', [AssetController::class, 'import'])
        ->name('assets.import');

    Route::get('/assets/template', [AssetController::class, 'downloadTemplate'])
        ->name('assets.template.download');

    /*
    |--------------------------------------------------------------------------
    | Core Resources
    |--------------------------------------------------------------------------
    */

    Route::resource('regions', RegionController::class);
    Route::resource('campuses', CampusController::class);
    Route::resource('assets', AssetController::class);

    /*
    |--------------------------------------------------------------------------
    | Agents
    |--------------------------------------------------------------------------
    */

    Route::get('/agents/invite', [AgentController::class, 'inviteForm'])->name('agents.invite');
    Route::post('/agents/invite', [AgentController::class, 'sendInvite'])->name('agents.sendInvite');
    Route::resource('agents', AgentController::class)->except(['show']);

    Route::post('/assets/preview', [AssetController::class, 'preview'])->name('assets.preview');
    Route::get('/assets/failed-download', [AssetController::class, 'downloadFailed'])->name('assets.failed.download');

    Route::delete('/agents/bulk-delete', [AgentController::class, 'bulkDelete'])->name('agents.bulkDelete');
});

/*
|--------------------------------------------------------------------------
| Profile Management
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

/*
|--------------------------------------------------------------------------
| Inline Edits Routes
|--------------------------------------------------------------------------
*/

Route::patch('/assets/{asset}/status', [AssetController::class, 'updateStatus'])
    ->name('assets.updateStatus');

/*
|--------------------------------------------------------------------------
| Roles Management
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

});

/*
|--------------------------------------------------------------------------
| Templates downloads
|--------------------------------------------------------------------------
*/

Route::get('/assets/template', [AssetController::class, 'downloadTemplate']); // fallback
Route::get('/assets/template/staff', [AssetController::class, 'downloadStaffTemplate']);
Route::get('/assets/template/student', [AssetController::class, 'downloadStudentTemplate']);
Route::get('/assets/download-failed', [AssetController::class, 'downloadFailed'])
    ->name('assets.downloadFailed');

/*
|--------------------------------------------------------------------------
| Asset Statuses
|--------------------------------------------------------------------------
*/

Route::prefix('settings')->group(function () {
    Route::get('/asset-statuses', [AssetStatusController::class, 'index'])->name('asset-statuses.index');
    Route::post('/asset-statuses', [AssetStatusController::class, 'store'])->name('asset-statuses.store');
    Route::put('/asset-statuses/{id}', [AssetStatusController::class, 'update'])->name('asset-statuses.update');
    Route::delete('/asset-statuses/{id}', [AssetStatusController::class, 'destroy'])->name('asset-statuses.destroy');
});

/*
|--------------------------------------------------------------------------
| Asset audits (🔥 ONLY CHANGE = wrapped in auth)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/audits', [AuditController::class, 'index'])->name('audits.index');
    Route::get('/audits/create', [AuditController::class, 'create'])->name('audits.create');
    Route::post('/audits', [AuditController::class, 'store'])->name('audits.store');

    Route::get('/audits/{audit}/verify', [AuditController::class, 'verify'])->name('audits.verify');

    Route::post('/audit-records/{record}', [AuditController::class, 'updateRecord'])
        ->name('audit.records.update');

    Route::post('/audit-records/{record}/approve', [AuditController::class, 'approve'])
        ->name('audit.records.approve');

    Route::post('/audit-records/bulk-update', [AuditController::class, 'bulkUpdate'])
        ->name('audit.records.bulk');

    Route::get('/audits/{audit}/dashboard', [AuditController::class, 'dashboard'])
        ->name('audits.dashboard');

    Route::get('/audits/{audit}/campus/{campus}', [AuditController::class, 'campus'])
        ->name('audits.campus');

    // KEEP THIS LAST
    Route::resource('audits', AuditController::class);

});

/*
|--------------------------------------------------------------------------
| Asset Assignments
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/assignments', [AssetAssignmentController::class, 'index'])->name('assignments.index');
    Route::post('/assignments', [AssetAssignmentController::class, 'store'])->name('assignments.store');
    Route::post('/assignments/{id}/return', [AssetAssignmentController::class, 'return'])->name('assignments.return');
});

Route::get('/assignments/create', [AssetAssignmentController::class, 'create'])->name('assignments.create');
/*
|--------------------------------------------------------------------------
| Asset Movements
|--------------------------------------------------------------------------
*/
Route::get('/movements', [AssetMovementController::class, 'index'])->name('movements.index');
Route::get('/movements/create', [AssetMovementController::class, 'create'])->name('movements.create');
Route::post('/movements', [AssetMovementController::class, 'store'])->name('movements.store');

Route::get('/assets/{id}/timeline', [AssetController::class, 'timeline'])->name('assets.timeline');

/*
|--------------------------------------------------------------------------
| Appearance
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'can:manage settings'])->group(function () {

    Route::get('/settings/appearance', [SettingsController::class, 'appearance'])
        ->name('settings.appearance');

    Route::post('/settings/appearance', [SettingsController::class, 'updateAppearance'])
        ->name('settings.appearance.update');

});


require __DIR__.'/auth.php';