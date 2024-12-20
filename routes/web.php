<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\UsuarioClientSideController;
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

Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
Route::resource('usuarios', UsuarioController::class)->except(['index']);

Route::get('/usuarios-client', [UsuarioClientSideController::class, 'index'])->name('usuarios-client.index');
Route::post('/usuarios-client', [UsuarioClientSideController::class, 'store'])->name('usuarios-client.store');
Route::put('/usuarios-client/{usuario}', [UsuarioClientSideController::class, 'update'])->name('usuarios-client.update');
Route::delete('/usuarios-client/{usuario}', [UsuarioClientSideController::class, 'destroy'])->name('usuarios-client.destroy');
