<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstructionController;
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

Route::get('/', function () {
    return view('home');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboardSportSection', function () {
    return view('dashboardSportSection');
})->name('dashboardSportSection');

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
