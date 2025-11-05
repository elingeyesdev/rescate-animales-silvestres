<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnimalController;
use App\Http\Controllers\AnimalProfileController;
use App\Http\Controllers\DispositionController;
use App\Http\Controllers\HealthRecordController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect('animals');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('animals', AnimalController::class);

Route::resource('animal-profiles', AnimalProfileController::class);

Route::resource('dispositions', DispositionController::class);

Route::resource('health-records', HealthRecordController::class);
