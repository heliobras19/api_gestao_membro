<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);
    Route::post('logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    Route::post('refresh', [\App\Http\Controllers\AuthController::class, 'refresh']);
    Route::post('me', [\App\Http\Controllers\AuthController::class, 'me']);
});


Route::group(['middleware' => 'api'], function ($router) {
    Route::resource('membro', \App\Http\Controllers\MembroController::class);
    Route::resource('orgao', \App\Http\Controllers\OrgaosController::class);
    Route::resource('comite', \App\Http\Controllers\ComiteController::class);
    Route::get('pais', [\App\Http\Controllers\PaisController::class, 'pais']);

    Route::get('bairros', [\App\Http\Controllers\PaisController::class, 'bairros']);
    Route::get('comite/byBairros', [\App\Http\Controllers\ComiteController::class, 'byBairros']);
    Route::resource('funcao', \App\Http\Controllers\FuncaoController::class)->except(['create', 'edit']);
    Route::get("teste", [\App\Http\Controllers\MembroController::class, 'teste']);
});

Route::get("/ola", function () {
    return "get em api testado";
});

Route::post('register', [\App\Http\Controllers\AuthController::class, 'register']);
