<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KpEvaluationController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\CompanyController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/evaluations/datatables', [KpEvaluationController::class, 'datatables'])->name('evaluations.datatables');
    Route::resource('evaluations', KpEvaluationController::class);

    Route::middleware(['role:admin,kaprodi'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/api/pembimbing-lapangan/{company}', [StudentController::class, 'getPembimbingLapangan'])->name('api.pembimbing-lapangan');
        Route::resource('users', UserController::class);
        Route::resource('students', StudentController::class);
        Route::resource('companies', CompanyController::class);
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    });

    Route::middleware(['role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/dashboard', function() {
            return view('dosen.dashboard');
        })->name('dashboard');
    });

    Route::middleware(['role:pembimbing_lapangan'])->prefix('pembimbing-lapangan')->name('pembimbing-lapangan.')->group(function () {
        Route::get('/dashboard', function() {
            return view('pembimbing-lapangan.dashboard');
        })->name('dashboard');
    });

    Route::middleware(['role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', function() {
            return view('mahasiswa.dashboard');
        })->name('dashboard');
    });
});
