<?php

use App\Http\Controllers\EventSocialMediaController;
use App\Http\Controllers\LaneController;
use App\Http\Controllers\RaceTypeTemplateController;
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
use App\Http\Controllers\EventDocumentController;
use App\Http\Controllers\RaceController;
use App\Http\Controllers\TabeleController;
use App\Http\Controllers\RegattaInformationController;
use App\Http\Controllers\BotManController;
use App\Http\Controllers\BotmanQuestionController;
use App\Http\Controllers\NewBotmanQuestionController;
use App\Http\Controllers\BacklinksController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\SporttypeController;
use App\Http\Controllers\RaceTypeController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\CoursedateController;
use App\Http\Controllers\MemberImageController;
use App\Http\Controllers\RegattaTeamController;
use App\Http\Controllers\FaqController ;
use App\Http\Controllers\RegattaSettingsController;

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
Route::get('/',                                            [HomeController::class, 'index']);
Route::get('/'.env('MENUE_ABTEILUNG').'/{sportTeam}', [HomeController::class, 'homeSportSelect']);
Route::get('/Bericht/{event}',                             [HomeController::class, 'eventShow']);
Route::get('/Information/{instructionSearch}',             [HomeController::class, 'instructionShow']);
Route::get('/Termine',                                     [HomeController::class, 'eventFutureAll']);
Route::get('/Berichte',                                    [HomeController::class, 'eventPastAll']);
Route::get('/Anfahrt',                                     [HomeController::class, 'journey']);
Route::get('/Impressum',                                   [HomeController::class, 'imprint']);
Route::get('/'.env('MENUE_VEREIN'),                   [HomeController::class, 'club']);
Route::get('/'.env('MENUE_VERBAND'),                  [HomeController::class, 'sporttype']);

Route::get('/Mitgliederbilder/{event_id}',                 [MemberImageController::class, 'create'])->name('memberImage.create');
Route::post('/Mitgliederbilder/uploade',                   [MemberImageController::class, 'store']) ->name('memberImage.store');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('admin.dashboard');
})->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->get('/Adminmenu', function () {
    return view('admin.adminmenu');
})->name('adminmenu');

Route::middleware(['auth:sanctum', 'verified'])->get('/Regattamenu', function () {
    return view('regattaManagement.regattaMenu');
})->name('regattaMenu');

/*  //livewire
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboardSportSection', function () {
    return view('admin.sportSection.dashboardSportSection');
})->name('dashboardSportSection');
*/

Route::get('/Abteilung/alle',                                    [SportSectionController::class, 'index'])     ->name('sportSection.index');
Route::get('/Abteilung/neu',                                     [SportSectionController::class, 'create'])    ->name('sportSection.create');
Route::post('/Abteilung/speichern',                              [SportSectionController::class, 'store'])     ->name('sportSection.store');
Route::get('/Abteilung/edit/{sportSection_id}',                  [SportSectionController::class, 'edit'])      ->name('sportSection.edit');
Route::post('/Abteilung/update/{sportSection_id}',               [SportSectionController::class, 'update'])    ->name('sportSection.update');
Route::get('/Abteilung/aktiv/{sportSection_id}',                 [SportSectionController::class, 'aktiv'])     ->name('sportSection.aktiv');
Route::get('/Abteilung/inaktiv/{sportSection_id}',               [SportSectionController::class, 'inaktiv'])   ->name('sportSection.inaktiv');
Route::get('/Abteilung/start/{sportSection_id}',                 [SportSectionController::class, 'start'])     ->name('sportSection.start');
Route::get('/Abteilung/softDelete/{sportSection_id}',            [SportSectionController::class, 'softDelete']);
Route::get('/Abteilung/picturedelete/{sportSection_id}',         [SportSectionController::class, 'pictureDelete']);
Route::get('/Abteilung/sportSectionSportTeam/{sportSection_id}', [SportSectionController::class, 'sportSectionSportTeam']);

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

Route::get('/Event/alle',                                 [EventController::class, 'index'])              ->name('event.index');
Route::get('/Eventvergangenheit/alle',                    [EventController::class, 'indexPast'])          ->name('event.indexPast');
Route::get('/Event/neu',                                  [EventController::class, 'create'])             ->name('event.create');
Route::get('/Abteilungsevent/neu/{sportSection_id}',      [EventController::class, 'createSportSection']) ->name('event.createSportSection');
Route::post('/Event/speichern',                           [EventController::class, 'store'])              ->name('event.store');
Route::get('/Event/edit/{event_id}',                      [EventController::class, 'edit'])               ->name('event.edit');
Route::post('/Event/update/{event_id}',                   [EventController::class, 'update'])             ->name('event.update');
Route::get('/Regatta/wahl/aktiv',                         [EventController::class, 'regattaAktivSelect']) ->name('event.indexRegattaAktiv');
Route::get('/Regatta/aktiv/{event_id}',                   [EventController::class, 'regattaAktiv'])       ->name('event.regattaAktiv');
Route::get('/Regatta/inaktiv/{event_id}',                 [EventController::class, 'regattaInaktiv'])     ->name('event.regattaInaktiv');
Route::get('/Regatta/alle',                               [EventController::class, 'indexRegatta'])       ->name('event.indexRegatta');
Route::get('/Regatta/{event_id}',                         [EventController::class, 'selectRegatta'])->whereNumber('event_id')->name('event.selectRegatta');

Route::get('/Eventgruppe/alle',                           [EventGroupController::class, 'index'])         ->name('eventGroup.index');
Route::get('/Eventgruppe/neu',                            [EventGroupController::class, 'create'])        ->name('eventGroup.create');
Route::post('/Eventgruppe/speichern',                     [EventGroupController::class, 'store'])         ->name('eventGroup.store');
Route::get('/Eventgruppe/edit/{eventGroup_id}',           [EventGroupController::class, 'edit'])          ->name('eventGroup.edit');
Route::post('/Eventgruppe/update/{eventGroup_id}',        [EventGroupController::class, 'update'])        ->name('eventGroup.update');
Route::get('/Eventgruppe/softDelete/{eventGroup_id}',     [EventGroupController::class, 'softDelete']);
Route::get('/Eventgruppe/aktiv/{sportSection_id}',        [EventGroupController::class, 'aktiv'])         ->name('eventGroup.aktiv');
Route::get('/Eventgruppe/inaktiv/{sportSection_id}',      [EventGroupController::class, 'inaktiv'])       ->name('eventGroup.inaktiv');

Route::get('/Bericht/alle/{event_id}',                    [ReportController::class, 'index'])             ->name('report.index');
Route::get('/Bericht/neu/{event_id}',                     [ReportController::class, 'create'])            ->name('report.create');
Route::post('/Bericht/speichern',                         [ReportController::class, 'store'])             ->name('report.store');
Route::get('/Bericht/edit/{report_id}',                   [ReportController::class, 'edit'])              ->name('report.edit');
Route::post('/Bericht/update/{report_id}',                [ReportController::class, 'update'])            ->name('report.update');
Route::get('/Bericht/aktiv/{report_id}',                  [ReportController::class, 'aktiv'])             ->name('report.aktiv');
Route::get('/Bericht/inaktiv/{report_id}',                [ReportController::class, 'inaktiv'])           ->name('report.inaktiv');
Route::get('/Bericht/webaktiv/{report_id}',               [ReportController::class, 'webaktiv'])          ->name('report.webaktiv');
Route::get('/Bericht/webinaktiv/{report_id}',             [ReportController::class, 'webinaktiv'])        ->name('report.webinaktiv');
Route::get('/Bericht/picturedelete/{report_id}',          [ReportController::class, 'pictureDelete']);
Route::get('/Bericht/imagedelete/{report_id}',            [ReportController::class, 'imageDelete']);
Route::get('/Bericht/maxtop/{report_id}',                 [ReportController::class, 'maxtop'])            ->name('report.maxtop');
Route::get('/Bericht/top/{report_id}',                    [ReportController::class, 'top'])               ->name('report.top');
Route::get('/Bericht/down/{report_id}',                   [ReportController::class, 'down'])              ->name('report.down');
Route::get('/Bericht/maxdown/{report_id}',                [ReportController::class, 'maxdown'])           ->name('report.maxdown');
Route::get('/Bericht/start/{report_id}',                  [ReportController::class, 'start'])             ->name('report.start');

// TEMP: Route nur für die Übername der Bilder zuständig
Route::get('/Berichtbilder/uebernehmen',                  [ReportController::class, 'takeover'])          ->name('report.takeover');

Route::get('/Instruction/alle',                           [InstructionController::class, 'index'])        ->name('instruction.index');
Route::get('/Instruction/neu',                            [InstructionController::class, 'create'])       ->name('instruction.create');
Route::post('/Instruction/speichern',                     [InstructionController::class, 'store'])        ->name('instruction.store');
Route::get('/Instruction/edit/{instruction_id}',          [InstructionController::class, 'edit'])         ->name('instruction.edit');
Route::post('/Instruction/update/{instruction_id}',       [InstructionController::class, 'update'])       ->name('instruction.update');
Route::get('/Instruction/aktiv/{instruction_id}',         [InstructionController::class, 'aktiv'])        ->name('instruction.aktiv');
Route::get('/Instruction/inaktiv/{instruction_id}',       [InstructionController::class, 'inaktiv'])      ->name('instruction.inaktiv');
Route::get('/Instruction/maxtop/{instruction_id}',        [InstructionController::class, 'maxtop'])       ->name('instruction.maxtop');
Route::get('/Instruction/top/{instruction_id}',           [InstructionController::class, 'top'])          ->name('instruction.top');
Route::get('/Instruction/down/{instruction_id}',          [InstructionController::class, 'down'])         ->name('instruction.down');
Route::get('/Instruction/maxdown/{instruction_id}',       [InstructionController::class, 'maxdown'])      ->name('instruction.maxdown');
Route::get('/Instruction/MenuNeu/{instruction_id}',       [InstructionController::class, 'menuNew'])      ->name('instruction.menuNew');
Route::get('/Instruction/MenuMinus/{instruction_id}',     [InstructionController::class, 'menuMinus'])    ->name('instruction.menuMinus');
Route::get('/Instruction/MenuPlus/{instruction_id}',      [InstructionController::class, 'menuPlus'])     ->name('instruction.menuPlus');
Route::get('/Instruction/MenuDelete/{instruction_id}',    [InstructionController::class, 'menuDelete'])   ->name('instruction.menuDelete');
Route::get('/Instruction/MenuDown/{instruction_id}',      [InstructionController::class, 'MenuDown'])     ->name('instruction.MenuDown');

Route::get('/newBotmanQuestion/alle',                            [NewBotmanQuestionController::class, 'index'])     ->name('newBotmanQuestion.index');
Route::get('/newBotmanQuestion/aktiv/{newBotmanQuestionId}',     [NewBotmanQuestionController::class, 'aktiv'])     ->name('newBotmanQuestion.aktiv');
Route::get('/newBotmanQuestion/inaktiv/{newBotmanQuestionId}',   [NewBotmanQuestionController::class, 'inaktiv'])   ->name('newBotmanQuestion.inaktiv');
Route::get('/newBotmanQuestion/softDelete/{newBotmanQuestionId}',[NewBotmanQuestionController::class, 'softDelete']);

Route::get('/Team/alle',                         [BoardController::class, 'index'])     ->name('board.index');
Route::get('/Team/neu',                          [BoardController::class, 'create'])    ->name('board.create');
Route::post('/Team/speichern',                   [BoardController::class, 'store'])     ->name('board.store');
Route::get('/Team/aktiv/{board_id}',             [BoardController::class, 'aktiv'])     ->name('board.aktiv');
Route::get('/Team/inaktiv/{board_id}',           [BoardController::class, 'inaktiv'])   ->name('board.inaktiv');
Route::get('/Team/maxtop/{board_id}',            [BoardController::class, 'maxtop'])    ->name('board.maxtop');
Route::get('/Team/top/{board_id}',               [BoardController::class, 'top'])       ->name('board.top');
Route::get('/Team/down/{board_id}',              [BoardController::class, 'down'])      ->name('board.down');
Route::get('/Team/maxdown/{board_id}',           [BoardController::class, 'maxdown'])   ->name('board.maxdown');
Route::get('/Team/edit/{board_id}',              [BoardController::class, 'edit'])      ->name('board.edit');
Route::post('/Team/update/{board_id}',           [BoardController::class, 'update'])    ->name('board.update');
Route::get('/Team/loeschen/{board_id}',          [BoardController::class, 'destroy'])   ->name('board.destroy');

Route::get('/Posten/{board_id}',                 [BoardUserController::class, 'index'])     ->name('boardUser.index');
Route::get('/Posten/neu/{board_id}',             [BoardUserController::class, 'create'])    ->name('boardUser.create');
Route::get('/Posten/aktiv/{boardUser_id}',       [BoardUserController::class, 'aktiv'])     ->name('boardUser.aktiv');
Route::get('/Posten/inaktiv/{boardUser_id}',     [BoardUserController::class, 'inaktiv'])   ->name('boardUser.inaktiv');
Route::get('/Posten/maxtop/{boardUser_id}',      [BoardUserController::class, 'maxtop'])    ->name('boardUser.maxtop');
Route::get('/Posten/top/{boardUser_id}',         [BoardUserController::class, 'top'])       ->name('boardUser.top');
Route::get('/Posten/down/{boardUser_id}',        [BoardUserController::class, 'down'])      ->name('boardUser.down');
Route::get('/Posten/maxdown/{boardUser_id}',     [BoardUserController::class, 'maxdown'])   ->name('boardUser.maxdown');
Route::get('/Posten/edit/{boardUser_id}',        [BoardUserController::class, 'edit'])      ->name('boardUser.edit');
Route::get('/Posten/zuordnen/{boardUser_id}',    [BoardUserController::class, 'match'])     ->name('boardUser.match');
Route::get('/Posten/loeschen/{boardUser_id}',    [BoardUserController::class, 'destroy'])   ->name('boardUser.destroy');

Route::get('/Dokumente/alle',                    [DocumentController::class, 'index'])         ->name('document.index');
Route::get('/Dokumente/neu',                     [DocumentController::class, 'create'])        ->name('document.create');
Route::post('/Dokumente/speichern',              [DocumentController::class, 'store'])         ->name('document.store');
Route::get('/Dokumente/edit/{document_id}',      [DocumentController::class, 'edit'])          ->name('document.edit');
Route::post('/Dokumente/update/{document_id}',   [DocumentController::class, 'update'])        ->name('document.update');
Route::get('/Dokumente/geloescht/{document_id}', [DocumentController::class, 'documentDelete'])->name('document.documentDelete');
Route::get('/Dokumente/aktiv/{document_id}',     [DocumentController::class, 'aktiv'])         ->name('document.aktiv');
Route::get('/Dokumente/inaktiv/{document_id}',   [DocumentController::class, 'inaktiv'])       ->name('document.inaktiv');

Route::get('/EventDokumente/{event_id}',                      [EventDocumentController::class, 'index'])             ->name('eventDocument.index');
Route::get('/EventDokumente/neu/{event_id}',                  [EventDocumentController::class, 'create'])            ->name('eventDocument.create');
Route::post('/EventDokumente/speichern/{event_id}',           [EventDocumentController::class, 'store'])             ->name('eventDocument.store');
Route::get('/EventDokumente/edit/{document_id}',              [EventDocumentController::class, 'edit'])              ->name('eventDocument.edit');
Route::post('/EventDokumente/update/{document_id}',           [EventDocumentController::class, 'update'])            ->name('eventDocument.update');
Route::get('/EventDokumente/geloescht/{document_id}',         [EventDocumentController::class, 'dokumentedelete'])   ->name('eventDocument.dokumentedelete');
Route::get('/EventDokumente/alt/geloescht/{document_id}',     [EventDocumentController::class, 'dokumenteAltdelete'])->name('eventDocument.dokumentAltedelete');
Route::get('/EventDokumente/aktiv/{document_id}',             [EventDocumentController::class, 'aktiv'])             ->name('eventDocument.aktiv');
Route::get('/EventDokumente/inaktiv/{document_id}',           [EventDocumentController::class, 'inaktiv'])           ->name('eventDocument.inaktiv');
Route::get('/EventDokumente/webaktiv/{document_id}',          [EventDocumentController::class, 'webaktiv'])          ->name('eventDocument.webaktiv');
Route::get('/EventDokumente/webinaktiv/{document_id}',        [EventDocumentController::class, 'webinaktiv'])        ->name('eventDocument.webinaktiv');
Route::get('/EventDokumente/maxtop/{document_id}',            [EventDocumentController::class, 'maxtop'])            ->name('eventDocument.maxtop');
Route::get('/EventDokumente/top/{document_id}',               [EventDocumentController::class, 'top'])               ->name('eventDocument.top');
Route::get('/EventDokumente/down/{document_id}',              [EventDocumentController::class, 'down'])              ->name('eventDocument.down');
Route::get('/EventDokumente/maxdown/{document_id}',           [EventDocumentController::class, 'maxdown'])           ->name('eventDocument.maxdown');
Route::get('/EventDokumente/Eintrag/geloescht/{document_id}', [EventDocumentController::class, 'destroy'])           ->name('eventDocument.destroy');

Route::get('/Event/SocialMedia/{event_id}',                   [EventSocialMediaController::class, 'index'])          ->name('socialMedia.index');
Route::get('/Event/SocialMedia/neu/{event_id}',               [EventSocialMediaController::class, 'create'])         ->name('socialMedia.create');
Route::post('/Event/SocialMedia/speichern/{socialMedia_id}',  [EventSocialMediaController::class, 'store'])          ->name('socialMedia.store');
Route::get('/Event/SocialMedia/edit/{socialMedia_id}',        [EventSocialMediaController::class, 'edit'])           ->name('socialMedia.edit');
Route::post('/Event/SocialMedia/update/{socialMedia_id}',     [EventSocialMediaController::class, 'update'])         ->name('socialMedia.update');
Route::get('/Event/SocialMedia/webaktiv/{socialMedia_id}',    [EventSocialMediaController::class, 'webaktiv'])       ->name('socialMedia.webaktiv');
Route::get('/Event/SocialMedia/webinaktiv/{socialMedia_id}',  [EventSocialMediaController::class, 'webinaktiv'])     ->name('socialMedia.webinaktiv');
Route::get('/Event/SocialMedia/aktiv/{socialMedia_id}',       [EventSocialMediaController::class, 'aktiv'])          ->name('socialMedia.aktiv');
Route::get('/Event/SocialMedia/inaktiv/{socialMedia_id}',     [EventSocialMediaController::class, 'inaktiv'])        ->name('socialMedia.inaktiv');
Route::get('/Event/SocialMedia/maxtop/{socialMedia_id}',      [EventSocialMediaController::class, 'maxtop'])         ->name('socialMedia.maxtop');
Route::get('/Event/SocialMedia/up/{socialMedia_id}',          [EventSocialMediaController::class, 'up'])             ->name('socialMedia.up');
Route::get('/Event/SocialMedia/down/{socialMedia_id}',        [EventSocialMediaController::class, 'down'])           ->name('socialMedia.down');
Route::get('/Event/SocialMedia/maxdown/{socialMedia_id}',     [EventSocialMediaController::class, 'maxdown'])        ->name('socialMedia.maxdown');

Route::get('/Backlink/alle',                          [BacklinksController::class, 'index'])         ->name('backlink.index');
Route::get('/Backlink/relevant',                      [BacklinksController::class, 'indexRelevant']) ->name('backlink.indexRelevant');
Route::get('/Backlink/benutzt',                       [BacklinksController::class, 'indexUsed'])     ->name('backlink.indexUsed');
Route::get('/Backlink/neu',                           [BacklinksController::class, 'create'])        ->name('backlink.create');
Route::post('/Backlink/speichern',                    [BacklinksController::class, 'store'])         ->name('backlink.store');
Route::get('/Backlink/aktiv/{backlink_id}',           [BacklinksController::class, 'aktiv'])         ->name('backlink.aktiv');
Route::get('/Backlink/inaktiv/{backlink_id}',         [BacklinksController::class, 'inaktiv'])       ->name('backlink.inaktiv');
Route::get('/Backlink/softDelete/{backlink_id}',      [BacklinksController::class, 'softDelete']);
Route::get('/Backlink/edit/{backlink_id}',            [BacklinksController::class, 'edit'])          ->name('backlink.edit');
Route::post('/Backlink/update/{backlink_id}',         [BacklinksController::class, 'update'])        ->name('backlink.update');

// Regattaverwaltung
// Regattateam bearbeiten
Route::get('/Regattateam/Werbungsquelle',             [RegattaTeamController::class, 'werbungsquelle'])->name('regattaTeam.werbungsquelle');
Route::get('/Regattateam/alle',                       [RegattaTeamController::class, 'index'])         ->name('regattaTeam.index');
Route::get('/Regattateam/neu',                        [RegattaTeamController::class, 'create'])        ->name('regattaTeam.create');
Route::post('/Regattateam/speichern',                 [RegattaTeamController::class, 'store'])         ->name('regattaTeam.store');
Route::get('/Regattateam/{regattaTeam_id}',           [RegattaTeamController::class, 'show'])          ->name('regattaTeam.show');
Route::get('/Regattateam/edit/{regattaTeam_id}',      [RegattaTeamController::class, 'edit'])          ->name('regattaTeam.edit');
Route::post('/Regattateam/update/{regattaTeam_id}',   [RegattaTeamController::class, 'update'])        ->name('regattaTeam.update');
Route::get('/Regattateam/Werbungsquelle/public/{regatta_id?}', [RegattaTeamController::class, 'werbungsquellePublic'])->name('regattaTeam.werbungsquellePublic');

Route::get('/Regatta/Einstellungen', [RegattaSettingsController::class, 'edit'])->name('regattaSettings.edit');
Route::post('/Regatta/Einstellungen', [RegattaSettingsController::class, 'update'])->name('regattaSettings.update');

Route::get('/Rennen/alle',                                            [RaceController::class, 'index'])           ->name('race.index');
Route::get('/Rennen/neu',                                            [RaceController::class, 'create'])          ->name('race.create');
Route::post('/Rennen/speichern',                                [RaceController::class, 'store'])           ->name('race.store');
Route::get('/Rennen/edit/{race_id}',                             [RaceController::class, 'edit'])            ->name('race.edit');
Route::post('/Rennen/update/{race_id}',                      [RaceController::class, 'update'])          ->name('race.update');
Route::get('/Rennen/aktiv/{race_id}',                            [RaceController::class, 'aktiv'])           ->name('race.aktiv');
Route::get('/Rennen/inaktiv/{race_id}',                         [RaceController::class, 'inaktiv'])         ->name('race.inaktiv');
Route::get('/Rennen/liveAktuell/aktiv/{race_id}',           [RaceController::class, 'aktivLive'])       ->name('race.aktivLive');
Route::get('/Rennen/liveAktuell/inaktiv/{race_id}',        [RaceController::class, 'inaktivLive'])     ->name('race.inaktivLive');
Route::get('/Rennen/Programm',                                 [RaceController::class, 'indexProgram'])    ->name('race.indexProgram');
Route::get('/Rennen/Programm/alle',                           [RaceController::class, 'indexProgramAll']) ->name('race.indexProgramAll');
Route::get('/Rennen/Programm/{race_id}',                   [RaceController::class, 'editProgram'])     ->name('race.editProgram');
Route::post('/Rennen/Programm/update/{race_id}',     [RaceController::class, 'updateProgram'])   ->name('race.updateProgram');
Route::get('/Rennen/Setzen/{race_id}',                        [RaceController::class, 'setProgram'])      ->name('race.setProgram');
Route::get('/Rennen/Programm/loeschen/{race_id}',   [RaceController::class, 'deleteProgram'])   ->name('race.deleteProgram');
Route::get('/Rennen/Ergebnisse',                               [RaceController::class, 'indexResult'])     ->name('race.indexResult');
Route::get('/Rennen/Ergebnisse/alle',                         [RaceController::class, 'indexResultAll'])  ->name('race.indexResultAll');
Route::get('/Rennen/Ergebnis/{race_id}',                     [RaceController::class, 'editResult'])      ->name('race.editResult');
Route::post('/Rennen/Ergebnis/update/{race_id}',       [RaceController::class, 'updateResult'])    ->name('race.updateResult');
Route::get('/Rennen/Ergebnis/loeschen/{race_id}',      [RaceController::class, 'deleteResult'])    ->name('race.deleteResult');
Route::get('/Rennen/Ergebniskontrolle',                      [RaceController::class, 'indexResultControll'])  ->name('race.indexResultControll');
Route::get('/Rennen/Zeit/{race_id}',                             [RaceController::class, 'raceTime'])        ->name('race.raceTime');
Route::post('/Rennen/Zeit/update/{race_id}',               [RaceController::class, 'updateRaceTime'])  ->name('race.updateRaceTime');
Route::get('/Rennen/sliteShowResult/activate/{id}',     [RaceController::class, 'sliteShowResultActivate']);
Route::get('/Rennen/sliteShowResult/deactivate/{id}', [RaceController::class, 'sliteShowResultDeactivate']);
Route::get('/Rennen/liveStream/activate/{id}',             [RaceController::class, 'liveStreamActivate']);
Route::get('/Rennen/liveStream/deactivate/{id}',         [RaceController::class, 'liveStreamDeactivate']);

Route::get('/Teamverlosung/{race_id}',                [LaneController::class, 'show'])            ->name('lane.show');
Route::get('/Teamverlosung/setzen/{race_id}',         [LaneController::class, 'editDraw'])        ->name('lane.editDraw');
Route::get('/Teamverlosung/Ergebnisse/{race_id}',     [LaneController::class, 'editResult'])      ->name('lane.editResult');
Route::post('/Teamverlosung/update/{race_id}',        [LaneController::class, 'update'])          ->name('lane.update');
Route::get('/Teamverlosung/platzierung/{race_id}',     [LaneController::class, 'editPlatzierung'])->name('lane.editPlatzierung');
Route::post('/Teamverlosung/platzierung/update/{race_id}', [LaneController::class, 'updatePlatzierung'])->name('lane.updatePlatzierung');
Route::post('/Rennergebnisse/update/{race_id}',       [LaneController::class, 'updateResult'])    ->name('lane.updateResult');
Route::get('/Teamverlosung/planen/{race_id}',         [LaneController::class, 'editSetDraw'])     ->name('lane.editSetDraw');
Route::post('/Teamverlosung/planen/update/{race_id}', [LaneController::class, 'updateSetDraw'])   ->name('lane.updateSetDraw');
Route::get('/Teamverlosung/Bahn/neu/{race_id}',       [LaneController::class, 'newLane'])         ->name('lane.newLane');
Route::get('/Teamverlosung/Bahn/loeschen/{race_id}',  [LaneController::class, 'deleteLast'])      ->name('lane.deleteLast');

// ToDo: Refactorieren {race_id} in {tabele_id} umbenennen
Route::get('/Tabelle/alle',                           [TabeleController::class, 'index'])                     ->name('tabele.index');
Route::get('/Tabelle/neu',                            [TabeleController::class, 'create'])                    ->name('tabele.create');
Route::post('/Tabelle/speichern',                     [TabeleController::class, 'store'])                     ->name('tabele.store');
Route::get('/Tabelle/edit/{tabele_id}',               [TabeleController::class, 'edit'])                      ->name('tabele.edit');
Route::post('/Tabelle/update/{tabele_id}',            [TabeleController::class, 'update'])                    ->name('tabele.update');
Route::get('/Tabelle/aktiv/{tabele_id}',              [TabeleController::class, 'aktiv'])                     ->name('tabele.aktiv');
Route::get('/Tabelle/inaktiv/{tabele_id}',            [TabeleController::class, 'inaktiv'])                   ->name('tabele.inaktiv');
Route::get('/Tabelle/Ergebnisse',                     [TabeleController::class, 'indexTabeleResoult'])        ->name('tabele.indexTabeleResoult');
Route::get('/Tabelle/ErgebnisseAlle',                 [TabeleController::class, 'indexTabeleResoultAll'])     ->name('tabele.indexTabeleResoultAll');
Route::get('/Tabelle/Ergebnis/{race_id}',             [TabeleController::class, 'editResult'])                ->name('tabele.editResult');
Route::post('/Tabelle/Ergebnis/update/{tabele_id}',   [TabeleController::class, 'updateResult'])              ->name('tabele.updateResult');
Route::get('/Tabelle/Ergebnis/loeschen/{tabele_id}',  [TabeleController::class, 'deleteResult'])              ->name('tabele.deleteResult');
Route::get('/Tabelle/anzeigen/{tabele_id}',           [TabeleController::class, 'show'])                      ->name('tabele.show');
Route::get('/Tabelle/verlosen/{tabele_id}',           [TabeleController::class, 'shuffel'])                   ->name('tabele.shuffel');
Route::get('/Tabelle/Konsistenzpruefung/{tabele_id}',               [TabeleController::class, 'consistency'])->name('tabele.consistency');

Route::get('/Renneninformation/alle',                          [RegattaInformationController::class, 'index'])   ->name('regattaInformation.index');
Route::get('/Renneninformation/neu',                           [RegattaInformationController::class, 'create'])  ->name('regattaInformation.create');
Route::post('/Renneninformation/speichern',                    [RegattaInformationController::class, 'store'])   ->name('regattaInformation.store');
Route::get('/Renneninformation/edit/{regattaInfo_id}',         [RegattaInformationController::class, 'edit'])    ->name('regattaInformation.edit');
Route::post('/Renneninformation/update/{regattaInfo_id}',      [RegattaInformationController::class, 'update'])  ->name('regattaInformation.update');
Route::get('/Renneninformation/aktiv/{regattaInfo_id}',        [RegattaInformationController::class, 'aktiv'])   ->name('regattaInformation.aktiv');
Route::get('/Renneninformation/inaktiv/{regattaInfo_id}',      [RegattaInformationController::class, 'inaktiv']) ->name('regattaInformation.inaktiv');
Route::get('/Renneninformation/maxtop/{regattaInfo_id}',       [RegattaInformationController::class, 'maxtop'])  ->name('regattaInformation.maxtop');
Route::get('/Renneninformation/top/{regattaInfo_id}',          [RegattaInformationController::class, 'top'])     ->name('regattaInformation.top');
Route::get('/Renneninformation/down/{regattaInfo_id}',         [RegattaInformationController::class, 'down'])    ->name('regattaInformation.down');
Route::get('/Renneninformation/maxdown/{regattaInfo_id}',      [RegattaInformationController::class, 'maxdown']) ->name('regattaInformation.maxdown');

Route::get('/Rennklassen/alle',                                [RaceTypeController::class, 'index'])          ->name('raceType.index');
Route::get('/Rennklassen/neu',                                 [RaceTypeController::class, 'create'])         ->name('raceType.create');
Route::get('/Rennklassen/speichern/{raceTypeTemplate_id}',     [RaceTypeController::class, 'store'])          ->name('raceType.store');
Route::get('/Rennklassen/edit/{raceType_id}',                  [RaceTypeController::class, 'edit'])           ->name('raceType.edit');
Route::post('/Rennklassen/update/{raceType_id}',               [RaceTypeController::class, 'update'])         ->name('raceType.update');

Route::get('/Rennklassenvorlage/alle',                         [RaceTypeTemplateController::class, 'index'])  ->name('raceTypeTemplate.index');
Route::get('/Rennklassenvorlage/neu',                          [RaceTypeTemplateController::class, 'create']) ->name('raceTypeTemplate.create');
Route::post('/Rennklassenvorlage/speichern',                   [RaceTypeTemplateController::class, 'store'])  ->name('raceTypeTemplate.store');
Route::get('/Rennklassenvorlage/edit/{raceTypeTemplate_id}',   [RaceTypeTemplateController::class, 'edit'])   ->name('raceTypeTemplate.edit');
Route::post('/Rennklassenvorlage/update/{raceTypeTemplate_id}',[RaceTypeTemplateController::class, 'update']) ->name('raceTypeTemplate.update');

// Regatta Einstellungen (muss vor /Regatta/{event_id} stehen)
// (moved to Regatta-Block above)
// Route::get('/Regatta/Einstellungen', [RegattaSettingsController::class, 'edit'])->name('regattaSettings.edit');
// Route::post('/Regatta/Einstellungen', [RegattaSettingsController::class, 'update'])->name('regattaSettings.update');

Route::get('/Club/alle',                                       [ClubController::class, 'index'])               ->name('club.index');
Route::get('/Club/neu',                                        [ClubController::class, 'create'])              ->name('club.create');
Route::post('/Club/speichern',                                 [ClubController::class, 'store'])               ->name('club.store');
Route::get('/Club/edit/{club_id}',                             [ClubController::class, 'edit'])                ->name('club.edit');
Route::post('/Club/update/{club_id}',                          [ClubController::class, 'update'])              ->name('club.update');
Route::get('/Club/softDelete/{club_id}',                       [ClubController::class, 'softDelete']);
Route::get('/Club/aktiv/{club_id}',                            [ClubController::class, 'aktiv'])               ->name('club.aktiv');
Route::get('/Club/inaktiv/{club_id}',                          [ClubController::class, 'inaktiv'])             ->name('club.inaktiv');
Route::get('/Club/{club_id}/Sportart/{sporttype_id}/attach',   [ClubController::class, 'clubAttachSporttype']) ->name('club.club_sporttype');
Route::get('/Club/{club_id}/Sportart/{sporttype_id}/detach',   [ClubController::class, 'clubDetachSporttype']) ->name('club.club_sporttype');

Route::get('/Sportart/alle',                                   [SporttypeController::class, 'index'])         ->name('sporttype.index');
Route::get('/Sportart/neu',                                    [SporttypeController::class, 'create'])        ->name('sporttype.create');
Route::post('/Sportart/speichern',                             [SporttypeController::class, 'store'])         ->name('sporttype.store');
Route::get('/Sportart/edit/{sporttype_id}',                    [SporttypeController::class, 'edit'])          ->name('sporttype.edit');
Route::post('/Sportart/update/{sporttype_id}',                 [SporttypeController::class, 'update'])        ->name('sporttype.update');
Route::get('/Sportart/softDelete/{sporttype_id}',              [SporttypeController::class, 'softDelete']);
Route::get('/Sportart/aktiv/{sporttype_id}',                   [SporttypeController::class, 'aktiv'])         ->name('sporttype.aktiv');
Route::get('/Sportart/inaktiv/{sporttype_id}',                 [SporttypeController::class, 'inaktiv'])       ->name('sporttype.inaktiv');

Route::get('/Training/alle/{sportSection_id}',                 [TrainingController::class, 'index'])  ->name('training.index');
Route::get('/Training/Abteilung/neu/{sportSection_id}',        [TrainingController::class, 'create']) ->name('training.create');
Route::post('/Training/speichern',                             [TrainingController::class, 'store'])  ->name('training.store');
Route::get('/Training/edit/{training_id}',                     [TrainingController::class, 'edit'])   ->name('training.edit');
Route::post('/Training/update/{training_id}',                  [TrainingController::class, 'update']) ->name('training.update');
Route::get('/Training/loeschen/{training_id}',                 [TrainingController::class, 'destroy'])->name('training.destroy');

// FAQ Verwaltung
Route::get('/FAQ/alle', [FaqController::class, 'index'])->name('faq.index');
Route::get('/FAQ/neu', [FaqController::class, 'create'])->name('faq.create');
Route::post('/FAQ/speichern', [FaqController::class, 'store'])->name('faq.store');
Route::get('/FAQ/edit/{faq_id}', [FaqController::class, 'edit'])->name('faq.edit');
Route::post('/FAQ/update/{faq_id}', [FaqController::class, 'update'])->name('faq.update');
Route::post('/FAQ/loeschen/{faq_id}', [FaqController::class, 'destroy'])->name('faq.destroy');
Route::get('/FAQ/top/{faq_id}', [FaqController::class, 'up'])->name('faq.up');
Route::get('/FAQ/down/{faq_id}', [FaqController::class, 'down'])->name('faq.down');
Route::get('/FAQ/aktiv/{faq_id}', [FaqController::class, 'aktiv'])->name('faq.aktiv');
Route::get('/FAQ/inaktiv/{faq_id}', [FaqController::class, 'inaktiv'])->name('faq.inaktiv');
Route::get('/FAQ/Kategorie/top/{categoryEncoded}', [FaqController::class, 'categoryUp'])->name('faq.category.up');
Route::get('/FAQ/Kategorie/down/{categoryEncoded}', [FaqController::class, 'categoryDown'])->name('faq.category.down');

Route::match(['get', 'post'], '/botman',                       [BotManController::class, 'handle']);
Route::resources([
    'BotmanQuestion' => BotmanQuestionController::class,
]);
