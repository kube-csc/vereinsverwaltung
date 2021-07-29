<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstructionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SportSectionController;
use App\Http\Controllers\BotManController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index']);

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('admin.dashboard');
})->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->get('/adminmenu', function () {
    return view('admin.adminmenu');
})->name('adminmenu');

/*  //livewire
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboardSportSection', function () {
    return view('admin.sportSection.dashboardSportSection');
})->name('dashboardSportSection');
*/

//Route::resource('sportSection', 'SportSectionController');
Route::get('/Abteilung/alle',                            [SportSectionController::class, 'index'])     ->name('sportSection.index');
Route::get('/Abteilung/neu',                             [SportSectionController::class, 'create'])    ->name('sportSection.create');
Route::post('/Abteilung/speichern',                      [SportSectionController::class, 'store'])     ->name('sportSection.store');
Route::get('/Abteilung/edit/{sportSection_id}',          [SportSectionController::class, 'edit'])      ->name('sportSection.edit');
Route::post('/Abteilung/update/{sportSection_id}',       [SportSectionController::class, 'update'])    ->name('sportSection.update');
Route::get('/Abteilung/aktiv/{sportSection_id}',         [SportSectionController::class, 'aktiv'])     ->name('sportSection.aktiv');
Route::get('/Abteilung/inaktiv/{sportSection_id}',       [SportSectionController::class, 'inaktiv'])   ->name('sportSection.inaktiv');
Route::get('/Abteilung/start/{sportSection_id}',         [SportSectionController::class, 'start'])     ->name('sportSection.start');
Route::get('/Abteilung/softDelete/{sportSection_id}',    [SportSectionController::class, 'softDelete']);
Route::get('/Abteilung/picturedelete/{sportSection_id}', [SportSectionController::class, 'pictureDelete']);

Route::resources([
    'instruction' => InstructionController::class,
]);

Route::get('/anfahrt', function () {
    return view('anfahrt');
});

Route::get('/impressum', function () {
    return view('impressum');
});

Route::get('/datenschutzerklaerung', function () {
    return view('datenschutzerklaerung');
});

Route::match(['get', 'post'], '/botman', [BotManController::class, 'handle']);

Route::resources([
    'BotmanQuestion' => BotmanQuestionController::class,
]);
