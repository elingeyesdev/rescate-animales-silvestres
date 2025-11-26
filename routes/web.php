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
use App\Http\Controllers\CareFeedingController;
use App\Http\Controllers\FeedingTypeController;
use App\Http\Controllers\FeedingFrequencyController;
use App\Http\Controllers\FeedingPortionController;
use App\Http\Controllers\IncidentTypeController;
use App\Http\Controllers\AnimalConditionController;
use App\Http\Controllers\Transactions\AnimalTransactionalController;
use App\Http\Controllers\Transactions\AnimalFeedingTransactionalController;
use App\Http\Controllers\Transactions\AnimalMedicalEvaluationTransactionalController;
use App\Http\Controllers\Transactions\AnimalCareTransactionalController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AnimalHistoryController;

Route::get('/', function () {
    return redirect('login'); // pantalla inicial: login
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->middleware('auth')->name('home');
Route::get('breeds/by-species/{species}', [BreedController::class, 'bySpecies'])->name('breeds.bySpecies');
Route::resource('centers', CenterController::class);
Route::resource('animals', AnimalController::class)->middleware('auth');
Route::resource('animal-profiles', AnimalProfileController::class);
Route::resource('dispositions', DispositionController::class);
Route::resource('health-records', HealthRecordController::class);
Route::resource('reports', ReportController::class)->middleware('auth');
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
Route::resource('care-feedings', CareFeedingController::class);
Route::resource('feeding-types', FeedingTypeController::class);
Route::resource('feeding-frequencies', FeedingFrequencyController::class);
Route::resource('feeding-portions', FeedingPortionController::class);
Route::resource('incident-types', IncidentTypeController::class)->middleware('auth');
Route::resource('animal-conditions', AnimalConditionController::class)->middleware('auth');

//Transaccionales
Route::resource('animal-records', AnimalTransactionalController::class)->middleware('auth');
Route::resource('animal-feeding-records', AnimalFeedingTransactionalController::class)->middleware('auth');
Route::resource('medical-evaluation-transactions', AnimalMedicalEvaluationTransactionalController::class)->middleware('auth');
Route::resource('animal-care-records', AnimalCareTransactionalController::class)->middleware('auth');
Route::resource('animal-histories', AnimalHistoryController::class)->only(['index','show'])->middleware('auth');