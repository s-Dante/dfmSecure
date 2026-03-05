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
Route::get("/login", function () {
    return view('auth.login');
})->name('logIn');
Route::get("/signin", function () {
    return view('auth.signin');
})->name('signIn');
Route::get("/verify-email", function () {
    return view('auth.verify-email');
})->name('verifyEmail');
Route::get("/verify-token", function () {
    return view('auth.verify-token');
})->name('verifyToken');
Route::get("/reset-password", function () {
    return view('auth.reset-pswd');
})->name('resetPassword');


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