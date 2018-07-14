<?php

namespace App;

use App\DetalleListaValorRelacional;


class CatalogoCitaNotiTools
{

  private $solicitud_required;
  private $log;
  private $workflow_type;

  public function __construct()
  {
      $this->log = new  \Log;
      $this->workflow_type = "solicitud_realizada";
  }

  public function pj_catalogo_citaciones($catalogo_id){
    $catalogo_arr = [];

    $catalogo = DetalleListaValorRelacional::where('id_principal_catalogo', $catalogo_id)->where('activo', 1)->get();

    if (is_null($catalogo)) { return json_encode($res); }

    foreach($catalogo as $cn) {
      $cata = new \stdClass;

      $cata->id = $cn->id_detalle_catalogo;
      $cata->valor = $cn->valor;

      $catalogo_arr[] = $cata;
      unset($cata);
    }

    $json_result = json_encode($catalogo_arr);
    return $json_result;

  }

}
