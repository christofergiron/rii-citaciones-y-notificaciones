<?php

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
    return view('welcome');
});

Route::post('/ss/new/solicitud_record_historial', 'API\SolicitudRecordHistorialController@store');
Route::post('/ss/show/solicitud_record_historial/{id}', 'API\SolicitudRecordHistorialController@show');
Route::post('/ss/tabla_solicitud_record_historial', "API\SolicitudRecordHistorialController@index");

Route::post('/ss/new/delito_contra_propiedad_ss', 'API\DelitoContraPropiedadSSController@store');
Route::post('/ss/show/delito_contra_propiedad_ss/{id}', 'API\DelitoContraPropiedadSSController@show');
Route::post('/ss/tabla_delito_contra_propiedad_ss', "API\DelitoContraPropiedadSSController@index");

Route::post('/ss/new/informe_logistico_ss', 'API\InformeLogisticoSSController@store');
Route::post('/ss/show/informe_logistico_ss/{id}', 'API\InformeLogisticoSSController@show');
Route::post('/ss/tabla_informe_logistico_ss', "API\InformeLogisticoSSController@index");

Route::post('/ss/new/informe_delito_comun_ss', 'API\InformeDelitoComunSSController@store');
Route::post('/ss/show/informe_delito_comun_ss/{id}', 'API\InformeDelitoComunSSController@show');
Route::post('/ss/tabla_informe_delito_comun_ss', "API\InformeDelitoComunSSController@index");

Route::post('/ss/new/informe_delito_contra_vida_ss', 'API\InformeDelitoContraVidaSSController@store');
Route::post('/ss/show/informe_delito_contra_vida_ss/{id}', 'API\InformeDelitoContraVidaSSController@show');
Route::post('/ss/tabla_informe_delito_contra_vida_ss', "API\InformeDelitoContraVidaSSController@index");

Route::post('/ss/new/informe_escena_delito_ss', 'API\InformeEscenaDelitoSSController@store');
Route::post('/ss/show/informe_escena_delito_ss/{id}', 'API\InformeEscenaDelitoSSController@show');
Route::post('/ss/tabla_informe_escena_delito_ss', "API\InformeEscenaDelitoSSController@index");

Route::post('/ss/new/captura', 'API\RealizarCapturaController@store');
Route::post('/ss/captura/{id}', 'API\RealizarCapturaController@show');
Route::post('/ss/tabla_capturas', 'API\RealizarCapturaController@index');
Route::post('/ss/new/flagrancia', "API\FlagranciaController@store");
