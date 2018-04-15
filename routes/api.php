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
#Route::resource('SolicitudRecordHistorial', 'API\SolicitudRecordHistorialController');
Route::resource('DelitoContraPropiedadSS', 'API\DelitoContraPropiedadSSController');
Route::resource('InformeLogisticoSS', 'API\InformeLogisticoSSController');
Route::resource('InformeDelitoContraVidaSS', 'API\InformeDelitoContraVidaSSController');
Route::resource('InformeDelitoComunSS', 'API\InformeDelitoComunSSController');
Route::resource('InformeEscenaDelitoSS', 'API\InformeEscenaDelitoSSController');
Route::resource('HitoSolicitudSS', 'API\HitoSolicitudSSController');

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

Route::post('/ss/new/hito_solicitud_ss', 'API\HitoSolicitudSSController@store');
Route::post('/ss/show/hito_solicitud_ss/{id}', 'API\HitoSolicitudSSController@show');
Route::post('/ss/tabla_hito_solicitud_ss', "API\HitoSolicitudSSController@index");

Route::post('/ss/new/hito_informe_ss', 'API\HitoInformeSSController@store');
Route::post('/ss/show/hito_informe_ss/{id}', 'API\HitoInformeSSController@show');
Route::post('/ss/tabla_hito_informe_ss', "API\HitoInformeSSController@index");

Route::post('/ss/tabla_informe', "API\InformeController@index");
Route::post('/ss/tabla_solicitud', "API\SolicitudController@index");

Route::post('/ss/new/captura', 'API\RealizarCapturaController@store');
Route::post('/ss/captura/{id}', 'API\RealizarCapturaController@show');
Route::post('/ss/tabla_capturas', 'API\RealizarCapturaController@index');
Route::post('/ss/new/flagrancia', "API\FlagranciaController@store");

Route::post('/ss/new/vehiculo', 'API\VehiculoController@store');
Route::post('/ss/vehiculo/{id}', 'API\VehiculoController@show');
Route::post('/ss/tabla_vehiculos', 'API\VehiculoController@index');

Route::post('/ss/new/dictamen', 'API\DictamenVehicularController@store');
Route::post('/ss/dictamen/{id}', 'API\DictamenVehicularController@show');
Route::post('/ss/tabla_dictamenes', 'API\DictamenVehicularController@index');

Route::post('/new/solicitud', 'API\SolicitudesController@store');
Route::post('/solicitud/{id}', 'API\SolicitudesController@show');
Route::post('/tabla_solicitudes', 'API\SolicitudesController@index');

Route::post('/pj/new/orden_captura', 'API\OrdenCapturaController@store');
Route::post('/pj/orden_captura/{id}', 'API\OrdenCapturaController@show');
Route::post('/pj/tabla_ordenes_captura', 'API\OrdenCapturaController@index');

Route::post('/persona/orden_captura/{id}', 'API\BuscarOrdenCapturaPersonaController@show_persona');

Route::post('/new/contra_orden_captura', 'API\ContraOrdenCapturaController@store');
Route::post('/contra_orden_captura/{id}', 'API\ContraOrdenCapturaController@show');
Route::post('/tabla_contra_ordenes_captura', 'API\ContraOrdenCapturaController@index');

Route::post('/new/solicitud_orden_captura', 'API\SolicitudOrdenController@store');
Route::post('/solicitud_orden_captura/{id}', 'API\SolicitudOrdenController@show');
Route::post('/tabla_solicitud_orden_captura', 'API\SolicitudOrdenController@index');

Route::post('/new/solicitud_contra_orden_captura', 'API\SolicitudContraOrdenController@store');
Route::post('/solicitud_contra_orden_captura/{id}', 'API\SolicitudContraOrdenController@show');
Route::post('/tabla_solicitud_contra_orden_captura', 'API\SolicitudContraOrdenController@index');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
