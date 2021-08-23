<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstructionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SportSectionController;
use App\Http\Controllers\EventGroupController;
use App\Http\Controllers\SportTeamController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BotManController;
use App\Http\Controllers\BotmanQuestionController;
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

Route::middleware(['auth:sanctum', 'verified'])->get('/Adminmenu', function () {
    return view('admin.adminmenu');
})->name('adminmenu');

/*  //livewire
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboardSportSection', function () {
    return view('admin.sportSection.dashboardSportSection');
})->name('dashboardSportSection');
*/

//Route::resource('sportSection', 'SportSectionController');
Route::get('/Abteilung/alle',                                     [SportSectionController::class, 'index'])     ->name('sportSection.index');
Route::get('/Abteilung/neu',                                      [SportSectionController::class, 'create'])    ->name('sportSection.create');
Route::post('/Abteilung/speichern',                               [SportSectionController::class, 'store'])     ->name('sportSection.store');
Route::get('/Abteilung/edit/{sportSection_id}',                   [SportSectionController::class, 'edit'])      ->name('sportSection.edit');
Route::post('/Abteilung/update/{sportSection_id}',                [SportSectionController::class, 'update'])    ->name('sportSection.update');
Route::get('/Abteilung/aktiv/{sportSection_id}',                  [SportSectionController::class, 'aktiv'])     ->name('sportSection.aktiv');
Route::get('/Abteilung/inaktiv/{sportSection_id}',                [SportSectionController::class, 'inaktiv'])   ->name('sportSection.inaktiv');
Route::get('/Abteilung/start/{sportSection_id}',                  [SportSectionController::class, 'start'])     ->name('sportSection.start');
Route::get('/Abteilung/softDelete/{sportSection_id}',             [SportSectionController::class, 'softDelete']);
Route::get('/Abteilung/picturedelete/{sportSection_id}',          [SportSectionController::class, 'pictureDelete']);
Route::get('/Abteilung/sportSectionSportTeam/{sportSection_id}',  [SportSectionController::class, 'sportSectionSportTeam']);

//Route::resource('sportTeam', 'SportTeamController');
Route::get('/Mannschaft/alle',                            [SportTeamController::class, 'index'])     ->name('sportTeam.index');
Route::get('/Mannschaft/neu/{sportSection_id}',           [SportTeamController::class, 'create'])    ->name('sportTeam.create');
Route::post('/Mannschaft/speichern',                      [SportTeamController::class, 'store'])     ->name('sportTeam.store');
Route::get('/Mannschaft/edit/{sportSection_id}',          [SportTeamController::class, 'edit'])      ->name('sportTeam.edit');
Route::post('/Mannschaft/update/{sportSection_id}',       [SportTeamController::class, 'update'])    ->name('sportTeam.update');
Route::get('/Mannschaft/aktiv/{sportSection_id}',         [SportTeamController::class, 'aktiv'])     ->name('sportTeam.aktiv');
Route::get('/Mannschaft/inaktiv/{sportSection_id}',       [SportTeamController::class, 'inaktiv'])   ->name('sportTeam.inaktiv');
Route::get('/Mannschaft/start/{sportSection_id}',         [SportTeamController::class, 'start'])     ->name('sportTeam.start');
Route::get('/Mannschaft/softDelete/{sportSection_id}',    [SportTeamController::class, 'softDelete']);
Route::get('/Mannschaft/picturedelete/{sportSection_id}', [SportTeamController::class, 'pictureDelete']);

//Route::resource('event', 'EventController');
Route::get('/Event/alle',                             [EventController::class, 'index'])              ->name('event.index');
Route::get('/Eventvergangenheit/alle',                [EventController::class, 'indexPast'])          ->name('event.indexPast');
Route::get('/Event/neu',                              [EventController::class, 'create'])             ->name('event.create');
Route::get('/Abteilungsevent/neu/{sportSection_id}',  [EventController::class, 'createSportSection']) ->name('event.createSportSection');
//Route::get('/Mannschaftsevent/neu/{sportSection_id}', [EventController::class, 'createSportTeam'])  ->name('event.createSportTeam');
Route::post('/Event/speichern',                       [EventController::class, 'store'])              ->name('event.store');
Route::get('/Event/edit/{event_id}',                  [EventController::class, 'edit'])               ->name('event.edit');
Route::post('/Event/update/{event_id}',               [EventController::class, 'update'])             ->name('event.update');

//Route::resource('Eventgruppe', 'EventGroupController');
Route::get('/Eventgruppe/alle',                        [EventGroupController::class, 'index'])         ->name('eventGroup.index');
Route::get('/Eventgruppe/neu',                         [EventGroupController::class, 'create'])        ->name('eventGroup.create');
Route::post('/Eventgruppe/speichern',                  [EventGroupController::class, 'store'])         ->name('eventGroup.store');
Route::get('/Eventgruppe/edit/{eventGroup_id}',        [EventGroupController::class, 'edit'])          ->name('eventGroup.edit');
Route::post('/Eventgruppe/update/{eventGroup_id}',     [EventGroupController::class, 'update'])        ->name('eventGroup.update');
Route::get('/Eventgruppe/softDelete/{eventGroup_id}',  [EventGroupController::class, 'softDelete']);

Route::resources([
    'instruction' => InstructionController::class,
]);

Route::get('/Anfahrt', function () {
    return view('anfahrt');
});

Route::get('/Impressum', function () {
    return view('impressum');
});

Route::get('/Datenschutzerklaerung', function () {
    return view('datenschutzerklaerung');
});

Route::match(['get', 'post'], '/botman', [BotManController::class, 'handle']);

Route::resources([
    'BotmanQuestion' => BotmanQuestionController::class,
]);
