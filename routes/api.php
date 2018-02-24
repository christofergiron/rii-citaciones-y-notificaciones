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

Route::post('/ss/new/solicitud_record_historial', 'API\SolicitudRecordHistorialController@store');
Route::post('/ss/show/solicitud_record_historial/{id}', 'API\SolicitudRecordHistorialController@show');
Route::post('/ss/tabla_solicitud_record_historial', "API\SolicitudRecordHistorialController@index");

Route::post('/ss/new/captura', 'API\RealizarCapturaController@store');
Route::post('/ss/captura/{id}', 'API\RealizarCapturaController@show');
Route::post('/ss/tabla_capturas', 'API\RealizarCapturaController@index');
Route::post('/ss/new/flagrancia', "API\FlagranciaController@store");

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
