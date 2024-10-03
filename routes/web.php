<?php
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkFlowController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return Auth::check() ? redirect('/login') : redirect('/users');
// });

// Route::get('/dashboard', [DashboardController::class, 'index'])
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::middleware('auth')->group(function () {

    // Log Routes
    Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

    // Profiles Route
    // Route::get('/update-password', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User Routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    // Notification Routes
    Route::post('/send-notifications', [NotificationController::class, 'sendNotification']);

    //Workflow Routes
    Route::get('/workflow', [WorkFlowController::class, 'index'])->name('workflow.index');
    Route::get('/workflow/settings/{id}', [WorkFlowController::class, 'edit'])->name('workflow.edit');
    Route::put('/workflow/setting/{id}', [WorkFlowController::class, 'update'])->name('workflow.update');
});

require __DIR__.'/auth.php';
