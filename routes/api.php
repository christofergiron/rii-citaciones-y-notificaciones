<?php

use Illuminate\Http\Request;

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
Route::resource('SolicitudRecordHistorial', 'API\SolicitudRecordHistorialController');
Route::resource('DelitoContraPropiedadSS', 'API\DelitoContraPropiedadSSController');
Route::resource('InformeLogisticoSS', 'API\InformeLogisticoSSController');
Route::resource('InformeDelitoContraVidaSS', 'API\InformeDelitoContraVidaSSController');
Route::resource('InformeDelitoComunSS', 'API\InformeDelitoComunSSController');
Route::resource('InformeEscenaDelitoSS', 'API\InformeEscenaDelitoSSController');

Route::post('/prueba_servicio', 'API\SolicitudRecordHistorialController@store');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
