<?php

use Illuminate\Support\Facades\Route;

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
});
Route::get("/verify-token", function () {
    return view('auth.verify-token');
});
Route::get("/reset-password", function () {
    return view('auth.reset-pswd');
});


/**
 * Rutas a vistas generales
 */
Route::get("/profile", function () {
    return view('profile');
});

Route::get("/dashboard", function () {
    return view('dashboard');
});

Route::get("/sinister-detail", function () {
    return view('sinister-detail');
});

Route::get("/consultation", function() {
    return view('consultation');
});


/**
 * Rutas del asegurado
 */
Route::get("/my-vehicle", function() {
    return view('insured.my-vehicles');
});

Route::get("/edit-vehicle/", function() {
    return view('insured.my-vehicles-edit');
});

Route::get("/my-policies", function() {
    return view('insured.my-policies');
});


/**
 * Rutas para el ajustador
 */
Route::get("/sinister-register", function() {
    return view('adjuster.sinister-register');
});

Route::get("/sinister-edit", function() {
    return view('adjuster.sinister-edit');
});


/**
 * Rutas para el supervisor
 */
Route::get("/search", function () {
    return view('supervisor.search');
});


/**
 * Rutas para el administrador
 */
Route::get("/manage", function () {
    return view('admin.employes-manage');
});