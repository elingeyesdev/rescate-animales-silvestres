<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReportApiController;
use App\Http\Controllers\Api\AnimalFileApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\AnimalCareApiController;
use App\Http\Controllers\Api\AnimalFeedingApiController;
use App\Http\Controllers\Api\AnimalMedicalEvaluationApiController;
use App\Http\Controllers\Api\AnimalHistoryApiController;
use App\Http\Controllers\Api\TransferApiController;
use App\Http\Controllers\Api\ReleaseApiController;
use App\Http\Controllers\Api\CenterApiController;
use App\Http\Controllers\Api\SpeciesApiController;
use App\Http\Controllers\Api\AnimalStatusApiController;
use App\Http\Controllers\Api\VeterinarianApiController;
use App\Http\Controllers\Api\TreatmentTypeApiController;
use App\Http\Controllers\Api\CareTypeApiController;
use App\Http\Controllers\Api\FirePredictionApiController;
use App\Http\Controllers\Api\AnimalConditionApiController;
use App\Http\Controllers\Api\IncidentTypeApiController;
use App\Http\Controllers\Api\RescuerApiController;
use App\Http\Controllers\Api\AnimalApiController;
use App\Http\Controllers\Api\PersonApiController;
use App\Http\Controllers\Api\WeatherApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::name('api.')->group(function () {
    Route::apiResource('login', AuthApiController::class)->only(['store']);
    Route::apiResource('reports', ReportApiController::class);
    Route::apiResource('animals', AnimalApiController::class)->only(['index', 'show']);
    Route::apiResource('animal-files', AnimalFileApiController::class)->only(['index', 'show', 'store']);
    Route::apiResource('animal-cares', AnimalCareApiController::class)->only(['index', 'show', 'store']);
    Route::apiResource('animal-feedings', AnimalFeedingApiController::class)->only(['index', 'show', 'store']);
    Route::apiResource('animal-medical-evaluations', AnimalMedicalEvaluationApiController::class)->only(['index', 'show', 'store']);
    Route::apiResource('animal-histories', AnimalHistoryApiController::class)->only(['index', 'show', 'store']);
    Route::apiResource('transfers', TransferApiController::class)->only(['index', 'show', 'store']);
    Route::apiResource('releases', ReleaseApiController::class)->only(['index', 'show', 'store']);
    Route::apiResource('users', UserApiController::class);
    Route::apiResource('people', PersonApiController::class)->only(['index', 'show']);
    Route::apiResource('centers', CenterApiController::class)->only(['index', 'show']);
    Route::apiResource('species', SpeciesApiController::class)->only(['index', 'show']);
    Route::apiResource('animal-statuses', AnimalStatusApiController::class)->only(['index', 'show']);
    Route::apiResource('veterinarians', VeterinarianApiController::class)->only(['index', 'show']);
    Route::apiResource('rescuers', RescuerApiController::class)->only(['index', 'show']);
    Route::apiResource('treatment-types', TreatmentTypeApiController::class)->only(['index', 'show']);
    Route::apiResource('care-types', CareTypeApiController::class)->only(['index', 'show']);
    Route::apiResource('fire-predictions', FirePredictionApiController::class)->only(['index']);
    Route::apiResource('animal-conditions', AnimalConditionApiController::class)->only(['index', 'show']);
    Route::apiResource('incident-types', IncidentTypeApiController::class)->only(['index', 'show']);
    
    Route::get('weather', [WeatherApiController::class, 'index'])->name('weather');
});
