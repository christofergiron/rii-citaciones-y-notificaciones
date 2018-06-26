<?php

namespace App;
use App\Workflow;
use Validator;
use App\Passport;
use App\Dependencia;
use App\Institucion;

class cmbdependenciassTools
{

  public function __construct()
  {
      $this->log = new  \Log;
  }

  private function headers(){
    $res = new \stdClass;
    $hdr = new \stdClass;
    $hdr->name = "id";
    $hdr->label = "id_dependencia";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "nombre";
    $hdr->label = "nombre_dependencia";
    $res->headers[] = $hdr;
    return $res->headers;
  }

  private function rows($token) {
    $res = new \stdClass;
    $dependencia = Dependencia::where('institucion_id', 3)->get();
    foreach ($dependencia as $dmp) {
      $row = new \stdClass;
      $row->id_dependencia = $dmp->id;
      $row->nombre_dependencia = $dmp->nombre;
      $res->rows[] = $row;
    }
    return $res->rows;
  }

  public function dependencias_ss($token) {
    $res = new \stdClass;
    $res->headers = $this->headers();
    $res->rows = $this->rows($token);
    return $res ;
  }
}
