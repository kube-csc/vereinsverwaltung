<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstructionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SportSectionController;
use App\Http\Controllers\EventGroupController;
use App\Http\Controllers\SportTeamController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\BoardUserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\BotManController;
use App\Http\Controllers\BotmanQuestionController;
use App\Http\Controllers\NewBotmanQuestionController;
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

//LandingPage
Route::get('/',                              [HomeController::class, 'index']);
Route::get('/Abteilung/detail/{sportTeam}',  [HomeController::class, 'homeSportSelect']);
Route::get('/Event/detail/{event}',          [HomeController::class, 'eventShow']);
Route::get('/Information/{event}',           [HomeController::class, 'instructionShow']);
Route::get('/EventFuture',                   [HomeController::class, 'eventFutureAll']);
Route::get('/EventPast',                     [HomeController::class, 'eventPastAll']);
Route::get('/Anfahrt',                       [HomeController::class, 'journey']);
Route::get('/Impressum',                     [HomeController::class, 'imprint']);

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
Route::get('/Eventgruppe/aktiv/{sportSection_id}',     [EventGroupController::class, 'aktiv'])         ->name('eventGroup.aktiv');
Route::get('/Eventgruppe/inaktiv/{sportSection_id}',   [EventGroupController::class, 'inaktiv'])       ->name('eventGroup.inaktiv');

//Route::resource('report', 'ReportController');
Route::get('/Bericht/alle/{event_id}',                 [ReportController::class, 'index'])             ->name('report.index');
Route::get('/Bericht/neu/{event_id}',                  [ReportController::class, 'create'])            ->name('report.create');
Route::post('/Bericht/speichern',                      [ReportController::class, 'store'])             ->name('report.store');
Route::get('/Bericht/edit/{event_id}',                 [ReportController::class, 'edit'])              ->name('report.edit');
Route::post('/Bericht/update/{event_id}',              [ReportController::class, 'update'])            ->name('report.update');
Route::get('/Bericht/aktiv/{board_id}',                [ReportController::class, 'aktiv'])             ->name('report.aktiv');
Route::get('/Bericht/inaktiv/{board_id}',              [ReportController::class, 'inaktiv'])           ->name('report.inaktiv');
Route::get('/Bericht/picturedelete/{sportSection_id}', [ReportController::class, 'pictureDelete']);

//Route::resource('instruction.', 'InstructionController');
Route::get('/Instruction/alle',                        [InstructionController::class, 'index'])        ->name('instruction.index');
Route::get('/Instruction/edit/{instruction_id}',       [InstructionController::class, 'edit'])         ->name('instruction.edit');
Route::post('/Instruction/update/{instruction_id}',    [InstructionController::class, 'update'])       ->name('instruction.update');
Route::get('/Instruction/aktiv/{instruction_id}',      [InstructionController::class, 'aktiv'])        ->name('instruction.aktiv');
Route::get('/Instruction/inaktiv/{instruction_id}',    [InstructionController::class, 'inaktiv'])      ->name('instruction.inaktiv');

//Route::resource('instruction.', 'NewBotmanQuestionController');
Route::get('/newBotmanQuestion/alle',                            [NewBotmanQuestionController::class, 'index'])     ->name('newBotmanQuestion.index');
Route::get('/newBotmanQuestion/aktiv/{newBotmanQuestionId}',     [NewBotmanQuestionController::class, 'aktiv'])     ->name('newBotmanQuestion.aktiv');
Route::get('/newBotmanQuestion/inaktiv/{newBotmanQuestionId}',   [NewBotmanQuestionController::class, 'inaktiv'])   ->name('newBotmanQuestion.inaktiv');
Route::get('/newBotmanQuestion/softDelete/{newBotmanQuestionId}',[NewBotmanQuestionController::class, 'softDelete']);

//Route::resource('board.', 'BoardController');
Route::get('/Team/alle',                            [BoardController::class, 'index'])     ->name('board.index');
Route::get('/Team/neu',                             [BoardController::class, 'create'])    ->name('board.create');
Route::post('/Team/speichern',                      [BoardController::class, 'store'])     ->name('board.store');
Route::get('/Team/aktiv/{board_id}',                [BoardController::class, 'aktiv'])     ->name('board.aktiv');
Route::get('/Team/inaktiv/{board_id}',              [BoardController::class, 'inaktiv'])   ->name('board.inaktiv');
Route::get('/Team/maxtop/{board_id}',               [BoardController::class, 'maxtop'])    ->name('board.maxtop');
Route::get('/Team/top/{board_id}',                  [BoardController::class, 'top'])       ->name('board.top');
Route::get('/Team/down/{board_id}',                 [BoardController::class, 'down'])      ->name('board.down');
Route::get('/Team/maxdown/{board_id}',              [BoardController::class, 'maxdown'])   ->name('board.maxdown');
Route::get('/Team/edit/{board_id}',                 [BoardController::class, 'edit'])      ->name('board.edit');
Route::post('/Team/update/{board_id}',              [BoardController::class, 'update'])    ->name('board.update');

//Route::resource('boardUser.', 'BoardUserController');
Route::get('/Posten/{board_id}',                     [BoardUserController::class, 'index'])     ->name('boardUser.index');
Route::get('/Posten/neu/{board_id}',                 [BoardUserController::class, 'create'])    ->name('boardUser.create');
Route::get('/Posten/aktiv/{boardUser_id}',           [BoardUserController::class, 'aktiv'])     ->name('boardUser.aktiv');
Route::get('/Posten/inaktiv/{boardUser_id}',         [BoardUserController::class, 'inaktiv'])   ->name('boardUser.inaktiv');
Route::get('/Posten/maxtop/{boardUser_id}',          [BoardUserController::class, 'maxtop'])    ->name('boardUser.maxtop');
Route::get('/Posten/top/{boardUser_id}',             [BoardUserController::class, 'top'])       ->name('boardUser.top');
Route::get('/Posten/down/{boardUser_id}',            [BoardUserController::class, 'down'])      ->name('boardUser.down');
Route::get('/Posten/maxdown/{boardUser_id}',         [BoardUserController::class, 'maxdown'])   ->name('boardUser.maxdown');
Route::get('/Posten/edit/{boardUser_id}',            [BoardUserController::class, 'edit'])      ->name('boardUser.edit');
Route::get('/Posten/zuordnen/{boardUser_id}',        [BoardUserController::class, 'match'])     ->name('boardUser.match');

//Route::resource('Document.', 'DocumentController');
Route::get('/Dokumente/alle',                        [DocumentController::class, 'index'])         ->name('document.index');
Route::get('/Dokumente/neu',                         [DocumentController::class, 'create'])        ->name('document.create');
Route::post('/Dokumente/speichern',                  [DocumentController::class, 'store'])         ->name('document.store');
Route::get('/Dokumente/edit/{document_id}',          [DocumentController::class, 'edit'])          ->name('document.edit');
Route::post('/Dokumente/update/{document_id}',       [DocumentController::class, 'update'])        ->name('document.update');
Route::get('/Dokumente/geloescht/{document_id}',     [DocumentController::class, 'documentDelete'])->name('document.documentDelete');
Route::get('/Dokumente/aktiv/{document_id}',         [DocumentController::class, 'aktiv'])         ->name('document.aktiv');
Route::get('/Dokumente/inaktiv/{document_id}',       [DocumentController::class, 'inaktiv'])       ->name('document.inaktiv');

/*
Route::get('/Impressum', function () {
    return view('impressum');
});
*/

Route::match(['get', 'post'], '/botman', [BotManController::class, 'handle']);

Route::resources([
    'BotmanQuestion' => BotmanQuestionController::class,
]);
