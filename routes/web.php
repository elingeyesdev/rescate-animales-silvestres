<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CenterController;
use App\Http\Controllers\AnimalController;
use App\Http\Controllers\AnimalProfileController;
use App\Http\Controllers\DispositionController;
use App\Http\Controllers\HealthRecordController;
use App\Http\Controllers\AnimalTypeController;
use App\Http\Controllers\AdoptionController;
use App\Http\Controllers\AnimalStatusController;
use App\Http\Controllers\CareTypeController;
use App\Http\Controllers\CareController;
use App\Http\Controllers\AnimalFileController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\SpeciesController;
use App\Http\Controllers\BreedController;
use App\Http\Controllers\ReleaseController;
use App\Http\Controllers\VeterinarianController;
use App\Http\Controllers\MedicalEvaluationController;
use App\Http\Controllers\TreatmentTypeController;
use App\Http\Controllers\RescuerController;
use App\Http\Controllers\TransferController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect('login'); // pantalla inicial: login
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('centers', CenterController::class);

Route::resource('animals', AnimalController::class)->middleware('auth'); // proteger animales

Route::resource('animal-profiles', AnimalProfileController::class);

Route::resource('dispositions', DispositionController::class);

Route::resource('health-records', HealthRecordController::class);

Route::resource('reports', ReportController::class);

Route::resource('animal-types', AnimalTypeController::class);

Route::resource('adoptions', AdoptionController::class);
Route::resource('animal-statuses', AnimalStatusController::class);
Route::resource('care-types', CareTypeController::class);
Route::resource('cares', CareController::class);
Route::resource('animal-files', AnimalFileController::class);
Route::resource('people', PersonController::class);
Route::resource('species', SpeciesController::class);
Route::resource('breeds', BreedController::class);
Route::resource('releases', ReleaseController::class);
Route::resource('veterinarians', VeterinarianController::class);
Route::resource('medical-evaluations', MedicalEvaluationController::class);
Route::resource('treatment-types', TreatmentTypeController::class);
Route::resource('rescuers', RescuerController::class);
Route::resource('transfers', TransferController::class);