<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstructionController;
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
