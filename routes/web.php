<?php

use App\Http\Controllers\MahasiswaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::fallback(function () {
  return response()->view('content.errors.404');
});

Route::controller(MahasiswaController::class)->group(function () {
  Route::get('/', 'index')->name('index');
  Route::get('/delete/{id}', 'delete')->name('mahasiswa-delete')->where('id', '[0-9]+');

  Route::post('/list', 'list')->name('mahasiswa-list');
  Route::post('/modal', 'modal')->name('mahasiswa-modal');
  Route::post('/_add', '_add');
  Route::post('/_edit', '_edit');
});
