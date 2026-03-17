<?php

use Illuminate\Support\Facades\Route;

use App\Models\Gender;

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
Route::get("/profile", function () {
    return view('profile');
})->name('profile');

Route::get("/dashboard", function () {
    return view('dashboard');
})->name('dashboard');

Route::get("/sinister-detail", function () {
    return view('sinister-detail');
})->name('sinisterDetail');

Route::get("/consultation", function () {
    return view('consultation');
})->name('consultation');


/**
 * Rutas del asegurado
 */
Route::get("/my-vehicle", function () {
    return view('insured.my-vehicles');
})->name('myVehicle');

Route::get("/edit-vehicle", function () {
    return view('insured.my-vehicles-edit');
})->name('editVehicle');

Route::get("/my-policies", function () {
    return view('insured.my-policies');
})->name('myPolicies');


/**
 * Rutas para el ajustador
 */
Route::get("/sinister-register", function () {
    return view('adjuster.sinister-register');
})->name('sinisterRegister');

Route::get("/sinister-edit", function () {
    return view('adjuster.sinister-edit');
})->name('sinisterEdit');


/**
 * Rutas para el supervisor
 */
Route::get("/search", function () {
    return view('supervisor.sinister-search');
})->name('search');

Route::get("/supervisor/manage", function () {
    return view('supervisor.sinister-manage');
})->name('sinisterManage');


/**
 * Rutas para el administrador
 */
Route::get("/manage", function () {
    return view('admin.employes-manage');
})->name('manage');