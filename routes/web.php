<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CenterController;
use App\Http\Controllers\AnimalController;
use App\Http\Controllers\AnimalProfileController;
use App\Http\Controllers\DispositionController;
use App\Http\Controllers\HealthRecordController;
use App\Http\Controllers\AnimalStatusController;
use App\Http\Controllers\CareTypeController;
use App\Http\Controllers\CareController;
use App\Http\Controllers\AnimalFileController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\SpeciesController;
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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\ReportsController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('home');
    }
    return redirect('landing');
});

Auth::routes();

// Ruta para refrescar token CSRF (sin middleware CSRF)
Route::get('/refresh-csrf', function () {
    return response()->json([
        'token' => csrf_token()
    ]);
})->middleware('web');

Route::get('/landing', [App\Http\Controllers\LandingController::class, 'index'])->name('landing');

Route::get('/reporte-rapido', function () {
    return view('quick-report');
})->name('reporte-rapido');

Route::post('/reporte-rapido', function () {
    return back();
})->name('reporte-rapido.store');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->middleware('auth')->name('home');
Route::get('/dashboard/export-pdf', [App\Http\Controllers\HomeController::class, 'exportPdf'])->middleware('auth')->name('dashboard.export-pdf');
Route::get('animal-histories/{animal_history}/pdf', [AnimalHistoryController::class, 'pdf'])->name('animal-histories.pdf')->middleware('auth');
Route::prefix('reports')->name('reports.')->group(function () {
    Route::put('{report}/approve', [ReportController::class, 'approve'])->name('approve')->middleware('auth');
    Route::get('claim', [ReportController::class, 'claim'])->name('claim');
    Route::post('claim', [ReportController::class, 'claimStore'])->name('claim.store');
    Route::get('mapa-campo', [ReportController::class, 'mapaCampo'])->name('mapa-campo')->middleware(['auth', 'role:admin|encargado']);
});

Route::resource('profile', ProfileController::class)->only(['index', 'update'])->middleware('auth');
Route::resource('contact-messages', ContactMessageController::class)->only(['store', 'update'])->middleware('auth');
Route::resource('centers', CenterController::class);
Route::resource('animals', AnimalController::class)->middleware('auth');
Route::resource('animal-profiles', AnimalProfileController::class);
Route::resource('dispositions', DispositionController::class);
Route::resource('health-records', HealthRecordController::class);
Route::resource('reports', ReportController::class);
Route::resource('animal-statuses', AnimalStatusController::class);
Route::resource('care-types', CareTypeController::class);
Route::resource('cares', CareController::class);
Route::resource('animal-files', AnimalFileController::class);
Route::resource('people', PersonController::class);
Route::post('people/{person}/convert-to-encargado', [PersonController::class, 'convertToEncargado'])->name('people.convert-to-encargado')->middleware('auth');
Route::resource('species', SpeciesController::class);
Route::resource('releases', ReleaseController::class);
Route::put('rescuers/{rescuer}/approve', [RescuerController::class, 'approve'])->name('rescuers.approve')->middleware('auth');
Route::resource('rescuers', RescuerController::class);
Route::put('veterinarians/{veterinarian}/approve', [VeterinarianController::class, 'approve'])->name('veterinarians.approve')->middleware('auth');
Route::resource('veterinarians', VeterinarianController::class);
Route::resource('medical-evaluations', MedicalEvaluationController::class);
Route::resource('treatment-types', TreatmentTypeController::class);
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

Route::get('reportes', [ReportsController::class, 'index'])->name('reportes.index')->middleware('auth');
Route::get('reportes/exportar-pdf', [ReportsController::class, 'exportPdf'])->name('reportes.export-pdf')->middleware('auth');
Route::get('reportes/exportar-csv', [ReportsController::class, 'exportCsv'])->name('reportes.export-csv')->middleware('auth');
