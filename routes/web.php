<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ThesisController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrowseController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\InstallController;

Route::get('/install', [InstallController::class, 'index'])->name('install.index');
Route::post('/install', [InstallController::class, 'process'])->name('install.process');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/browse', [BrowseController::class, 'index'])->name('browse');
Route::get('/search', [BrowseController::class, 'index']);
Route::get('/faq', function () {
    return view('faq');
})->name('faq');

// OAI-PMH Endpoint for Metadata Harvesting
Route::get('/oai', [App\Http\Controllers\OaiController::class, 'index'])->name('oai.index');

// Fallback route to serve storage files if symlink is missing
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) abort(404);
    $file = file_get_contents($fullPath);
    $type = mime_content_type($fullPath);
    return response($file, 200)->header('Content-Type', $type);
})->where('path', '.*');

// Fallback route to serve images from public/images if Apache fails
Route::get('/images/{path}', function ($path) {
    $fullPath = public_path('images/' . $path);
    if (!file_exists($fullPath)) abort(404);
    $file = file_get_contents($fullPath);
    $type = mime_content_type($fullPath);
    return response($file, 200)->header('Content-Type', $type);
})->where('path', '.*');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::get('/verify/{hash}', [HomeController::class, 'verify'])->name('verify');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', function () {
            if (auth()->user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return view('dashboard');
        })->name('dashboard');

        Route::get('/profile', function () {
            return view('profile');
        })->name('profile');
        Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');

        // Static Routes First
        Route::get('/theses/upload', [ThesisController::class, 'create'])->name('theses.create');
        Route::post('/theses/upload', [ThesisController::class, 'store'])->name('theses.store');

        // Notifications (Common for all roles)
        Route::post('/notifications/read-all', function() {
            auth()->user()->unreadNotifications->markAsRead();
            return response()->json(['success' => true]);
        })->name('notifications.read_all');

        Route::post('/notifications/{id}/read', function($id) {
            $notification = auth()->user()->notifications()->findOrFail($id);
            $notification->markAsRead();
            return response()->json(['success' => true]);
        })->name('notifications.read');
    });

    // Wildcard Routes Later
    Route::get('/theses/{thesis}', [ThesisController::class, 'show'])->name('theses.show');

    Route::middleware(['auth'])->group(function () {
        // Protected Reading & Downloading
        Route::get('/theses/{thesis}/read', [ThesisController::class, 'read'])->name('theses.read');
        Route::get('/theses/{thesis}/stream', [ThesisController::class, 'stream'])->name('theses.stream');
        Route::get('/theses/{thesis}/download', [ThesisController::class, 'download'])->name('theses.download');
        Route::get('/theses/file/{file}/download', [ThesisController::class, 'downloadFile'])->name('theses.download.file');
        Route::get('/theses/file/{file}/stream', [ThesisController::class, 'streamFile'])->name('theses.file.stream');
        Route::get('/theses/{thesis}/certificate', [ThesisController::class, 'certificate'])->name('theses.certificate');
    });

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/queue', [AdminController::class, 'queue'])->name('admin.queue');
    
    // Master Data
    // Manajemen Skripsi
    Route::get('/theses', [AdminController::class, 'theses'])->name('admin.theses.index');

    // Master Data (Super Admin Only)
    Route::middleware(['superadmin'])->group(function () {
        Route::get('/faculties', [AdminController::class, 'faculties'])->name('admin.master.faculties');
        Route::post('/faculties', [AdminController::class, 'storeFaculty'])->name('admin.master.faculties.store');
        Route::post('/faculties/{faculty}', [AdminController::class, 'updateFaculty'])->name('admin.master.faculties.update');
        Route::delete('/faculties/{faculty}', [AdminController::class, 'destroyFaculty'])->name('admin.master.faculties.destroy');
        Route::get('/departments', [AdminController::class, 'departments'])->name('admin.master.departments');
        Route::post('/departments', [AdminController::class, 'storeDepartment'])->name('admin.master.departments.store');
        Route::post('/departments/{department}', [AdminController::class, 'updateDepartment'])->name('admin.master.departments.update');
        Route::delete('/departments/{department}', [AdminController::class, 'destroyDepartment'])->name('admin.master.departments.destroy');
        Route::get('/types', [AdminController::class, 'types'])->name('admin.master.types');
        Route::post('/types', [AdminController::class, 'storeType'])->name('admin.master.types.store');
        Route::post('/types/{type}', [AdminController::class, 'updateType'])->name('admin.master.types.update');
        Route::delete('/types/{type}', [AdminController::class, 'destroyType'])->name('admin.master.types.destroy');

        // Manajemen User
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users.index');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::post('/users/{user}/verify', [AdminController::class, 'verifyUser'])->name('admin.users.verify');
        Route::post('/users/{user}/update', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::post('/users/{user}/password', [AdminController::class, 'updateUserPassword'])->name('admin.users.password');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

        // Pengaturan & Laporan
        Route::get('/settings', [AdminController::class, 'siteSettings'])->name('admin.settings');
        Route::post('/settings', [AdminController::class, 'updateSiteSettings'])->name('admin.settings.update');
        Route::post('/settings/queue-restart', [AdminController::class, 'restartQueue'])->name('admin.settings.queue_restart');
        Route::get('/settings/backup', [AdminController::class, 'downloadDatabase'])->name('admin.settings.backup');

        // System Control
        Route::get('/system/control', [AdminController::class, 'systemControl'])->name('admin.system.control');
        Route::post('/system/run-command', [AdminController::class, 'runArtisanCommand'])->name('admin.system.run_command');
        Route::post('/system/maintenance', [AdminController::class, 'toggleMaintenance'])->name('admin.system.maintenance');

        // Pengaturan Sertifikat
        Route::get('/certificates/settings', [AdminController::class, 'certificateSettings'])->name('admin.certificates.settings');
        Route::post('/certificates/settings', [AdminController::class, 'updateCertificateSettings'])->name('admin.certificates.settings.update');
    });
    
    // System Reports (Accessible by all Admins)
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports.index');

    // Data Surat / Sertifikat
    Route::get('/certificates', [AdminController::class, 'certificates'])->name('admin.certificates.index');

    Route::post('/theses/{thesis}/approve', [AdminController::class, 'approve'])->name('admin.theses.approve');
    Route::post('/theses/{thesis}/reject', [AdminController::class, 'reject'])->name('admin.theses.reject');
    Route::post('/theses/{thesis}/reset', [AdminController::class, 'resetStatus'])->name('admin.theses.reset');
    Route::post('/theses/{thesis}/update', [AdminController::class, 'updateThesis'])->name('admin.theses.update');
    Route::delete('/theses/{thesis}', [AdminController::class, 'destroyThesis'])->name('admin.theses.destroy');
    Route::post('/theses/{thesis}/upload-file', [AdminController::class, 'uploadFile'])->name('admin.theses.upload_file');
    Route::post('/theses/manual', [AdminController::class, 'storeManualThesis'])->name('admin.theses.store_manual');
    Route::get('/theses/export', [AdminController::class, 'exportTheses'])->name('admin.theses.export');
    Route::post('/theses/import', [AdminController::class, 'importTheses'])->name('admin.theses.import');
    Route::get('/certificates/{thesis}/print', [AdminController::class, 'printCertificate'])->name('admin.certificates.print');
    Route::post('/certificates/{thesis}/update', [AdminController::class, 'updateCertificate'])->name('admin.certificates.update');
    Route::post('/certificates/{thesis}/resend', [AdminController::class, 'resendCertificate'])->name('admin.certificates.resend');

});
