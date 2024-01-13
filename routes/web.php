<?php

use Illuminate\Support\Facades\Route;

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
    return "API FOR DATA SEND";
});

Route::get('excel',  [\App\Http\Controllers\PaisController::class, 'importarDadosView']);
Route::post('importar', [\App\Http\Controllers\PaisController::class, 'importarDados']);

Route::get("/ola", function () {
    return "Post testado";
});
