<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SinisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\MediaController;

/**
 * Landing page
 */
Route::get('/', function () {
    return view('welcome');
})->name('landingPage');

/**
 * Rutas de autentificacion 
 */
Route::middleware('guest')->group(function () {
    Route::get("/login", [App\Http\Controllers\AuthController::class, 'showLogin'])->name('logIn');
    Route::post("/login", [App\Http\Controllers\AuthController::class, 'login'])->name('logIn.post');

    Route::get("/signin", [App\Http\Controllers\AuthController::class, 'showRegister'])->name('signIn');
    Route::post("/signin", [App\Http\Controllers\AuthController::class, 'register'])->name('signIn.post');

    // Recovery routes
    Route::get("/verify-email", [App\Http\Controllers\PasswordResetController::class, 'showVerifyEmail'])->name('verifyEmail');
    Route::post("/verify-email", [App\Http\Controllers\PasswordResetController::class, 'sendToken'])->name('password.email');

    Route::get("/verify-token", [App\Http\Controllers\PasswordResetController::class, 'showVerifyToken'])->name('verifyToken');
    Route::post("/verify-token", [App\Http\Controllers\PasswordResetController::class, 'verifyToken'])->name('password.verifyToken');

    Route::get("/reset-password", [App\Http\Controllers\PasswordResetController::class, 'showResetPassword'])->name('resetPassword');
    Route::post("/reset-password", [App\Http\Controllers\PasswordResetController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout')->middleware('auth');

/**
 * Rutas a vistas generales
 */
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Detalle + comentarios del siniestro
    Route::get('/sinister-detail/{id}', [SinisterController::class, 'show'])->name('sinisterDetail');
    Route::post('/sinister-detail/{id}/comment', [SinisterController::class, 'addComment'])->name('sinisterComment');

    Route::get('/consultation', [ConsultationController::class, 'index'])->name('consultation');

    // Media streaming (blobs)
    Route::get('/media/sinister/{id}', [MediaController::class, 'sinister'])->name('media.sinister');
    Route::get('/media/profile/{userId}', [MediaController::class, 'profile'])->name('media.profile');
});


/**
 * Rutas del asegurado
 */
Route::middleware(['auth', 'role:insured'])->group(function () {
    Route::get("/my-vehicles", [\App\Http\Controllers\InsuredVehicleController::class, 'index'])->name('myVehicles');
    Route::post("/my-vehicles", [\App\Http\Controllers\InsuredVehicleController::class, 'store'])->name('myVehicles.store');
    Route::get("/my-vehicles/{id}/edit", [\App\Http\Controllers\InsuredVehicleController::class, 'edit'])->name('myVehicles.edit');
    Route::put("/my-vehicles/{id}", [\App\Http\Controllers\InsuredVehicleController::class, 'update'])->name('myVehicles.update');
    Route::delete("/my-vehicles/{id}", [\App\Http\Controllers\InsuredVehicleController::class, 'destroy'])->name('myVehicles.destroy');

    Route::get("/my-policies", [\App\Http\Controllers\InsuredPolicyController::class, 'index'])->name('myPolicies');
    Route::get("/my-policies/create", [\App\Http\Controllers\InsuredPolicyController::class, 'create'])->name('myPolicies.create');
    Route::post("/my-policies", [\App\Http\Controllers\InsuredPolicyController::class, 'store'])->name('myPolicies.store');
});


/**
 * Rutas para el ajustador
 */
Route::middleware(['auth', 'role:adjuster'])->group(function () {
    Route::get("/sinister-register", function () {
        return view('adjuster.sinister-register');
    })->name('sinisterRegister');

    Route::get("/sinister-edit", function () {
        return view('adjuster.sinister-edit');
    })->name('sinisterEdit');
});



/**
 * Rutas para el supervisor
 */
Route::middleware(['auth', 'role:supervisor'])->group(function () {
    Route::get("/search", function () {
        return view('supervisor.sinister-search');
    })->name('search');

    Route::get("/supervisor/manage", function () {
        return view('supervisor.sinister-manage');
    })->name('sinisterManage');
});



/**
 * Rutas para el administrador
 */
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/manage', [\App\Http\Controllers\AdminController::class, 'index'])->name('manage');
    Route::post('/manage', [\App\Http\Controllers\AdminController::class, 'store'])->name('manage.store');
    Route::get('/manage/{id}/edit', [\App\Http\Controllers\AdminController::class, 'edit'])->name('manage.edit');
    Route::put('/manage/{id}', [\App\Http\Controllers\AdminController::class, 'update'])->name('manage.update');
    Route::delete('/manage/{id}', [\App\Http\Controllers\AdminController::class, 'destroy'])->name('manage.destroy');
    Route::patch('/manage/{id}/restore', [\App\Http\Controllers\AdminController::class, 'restore'])->name('manage.restore');
});
