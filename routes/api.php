<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BancosAtuorizadosController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GestaoQuotasController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\PaisController;
use App\Http\Controllers\QuotaController;
use App\Http\Controllers\QuotasRelatorio;
use App\Http\Controllers\TipoQuotaController;
use App\Models\Localizacao\Comite;
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
    Route::put('update/{user}', [\App\Http\Controllers\AuthController::class, 'updateUser']);
    Route::put('user/{user}', [\App\Http\Controllers\AuthController::class, 'ativarConta']);
    Route::put('desativar/{user}', [\App\Http\Controllers\AuthController::class, 'desativarConta']);
    Route::get('users', [\App\Http\Controllers\AuthController::class, 'listUser']);
});


Route::group(['middleware' => 'api'], function ($router) {
    Route::resource('membro', \App\Http\Controllers\MembroController::class);
    Route::resource('orgao', \App\Http\Controllers\OrgaosController::class);
    Route::resource('comite', \App\Http\Controllers\ComiteController::class);
    Route::resource('funcao', \App\Http\Controllers\FuncaoController::class)->except(['create', 'edit']);
    Route::get('loc',  [\App\Http\Controllers\ComiteController::class, 'ola']);
    Route::get('pais', [\App\Http\Controllers\PaisController::class, 'pais']);
    Route::get('pais_all', [PaisController::class, 'paisSemAbragencia']);
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('bairros', [\App\Http\Controllers\PaisController::class, 'bairros']);
    Route::get("comite_by_bairros", [\App\Http\Controllers\ComiteController::class, 'byBairros']);
    Route::put('update/me/{user}', [AuthController::class, 'updateUser']);
    Route::get('only_user/{id}', [AuthController::class, 'onlyUser']);
    Route::post('password_verify', [AuthController::class, 'password_verify']);

    //rotas pagamento
    Route::post('pagamento', [PagamentoController::class, 'store']);
    Route::get('pagamento/consultar/{id}', [PagamentoController::class, 'consultarPagamento']);
    Route::get('pagamento/membro/{id}', [PagamentoController::class, 'membroPagamento']);
    Route::get('pagamento/membros/pesquisar', [PagamentoController::class, 'pesquisar']);

    //Rotas tipo quota
    Route::resource('tipo_quota', TipoQuotaController::class);

    //Rotas gestão de membros
    Route::get('gestao_quadro', [GestaoQuotasController::class, 'index']);
    Route::post('gestao_quadro/{id}', [GestaoQuotasController::class, 'store']);

    //Rotas bancos
    Route::resource('bancos_autorizados', BancosAtuorizadosController::class);

    //Rota relatorio quotas
    Route::get('quotas/devedores', [QuotasRelatorio::class, 'devedores']);

    Route::get('quotas/comitas_setorial', [QuotaController::class, 'setoriais']);
});

Route::get("/teste", function () {
    $comite = Comite::find('bed43ccc-8fdc-11ee-a6e7-00155d280abc');
    $setor =  $comite->comiteSetorial();
    return $setor->arvore();
});



Route::post('register', [\App\Http\Controllers\AuthController::class, 'register']);
