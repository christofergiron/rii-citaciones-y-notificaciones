<?php

namespace App;
use App\Workflow;
use Validator;
use App\Passport;
use App\Funcionario;
use App\FuncionarioPJ;
use App\Juez;
use App\ProcesoJudicial;
use App\AsignacionJuez;
use App\Lugar;
use App\LugarPJ;
use App\Documento;
use App\Denuncia;
use App\OrdenCaptura;
use App\OrdenCapturaDelito;
use App\OrdenCapturaEstado;
use App\OrdenCapturaMenor;
use App\OrdenCapturaPersona;
use App\OrdenCapturaPersonaMenor;
use App\OrdenCapturaPersonaNatural;
use App\OrdenCapturaVehiculo;
use App\OrdenCapturaXVehiculo;
use App\ContraOrdenCaptura;
use App\Expediente;
use App\ExpedientePJ;
use App\ExpedienteMP;
use App\ExpedienteSS;
use App\Rol;
use App\PersonaNatural;
use App\Persona;
use App\Dependencia;
use App\Institucion;

class BusquedaOrdenTools
{

  private $captura_required;
  private $log;
  private $workflow_type;

  public function __construct()
  {
      $this->log = new  \Log;
      $this->workflow_type = "captura_realizada";
  }

  private function get_persona_natural($id) {
    $persona_natural = PersonaNatural::find($id);
    if (is_null($persona_natural)) {return null;}
    return $persona_natural;
  }

  private function juez($id) {
      $numero_juez = "null";
      $orden_captura = OrdenCaptura::find($id);
      $id_juez = $orden_captura->id_juez;
      if (is_null($id_juez)) {return $numero_juez;}
      //cambio, descomentar esto
      //$juez = Juez::find($id_juez);
      //if (is_null($juez)) {return $numero_juez;}
      //$numero_juez = $juez->codigo;
      $numero_juez = $orden_captura->id_juez;
      return $numero_juez;
  }

  private function etapa($id) {
      $tipo_etapa = "null";
      $orden_captura = OrdenCaptura::find($id);
      $id_etapa = $orden_captura->id_etapa;
      if (is_null($id_etapa)) {return $tipo_etapa;}
      //$etapa Etapa::find($id_etapa);
      //if (is_null($etapa)) {return $tipo_etapa;}
      $tipo_etapa = $etapa->nombre;

      return $tipo_etapa;
  }

  private function audiencia($id) {
     //cambio esta vaina lleva herencia cuidado, no esta terminada
      $tipo_audiencia = "null";
      $orden_captura = OrdenCaptura::find($id);
      $id_audiencia = $orden_captura->id_etapa;
      if (is_null($id_audiencia)) {return $tipo_audiencia;}
      //$audiencia Audiencia::find($id_audiencia);
      //if (is_null($audiencia)) {return $tipo_audiencia;}
      $tipo_audiencia = $audiencia->nombre;

      return $tipo_audiencia;
  }

  private function get_email_from_token($token) {
    $user_email = "";
    $passport = new Passport;
    $res = $passport->details($token);

    if (!isset($res)) {return $user_email; }
    if (!($res->code == 200)) {return $user_email; }

    $this->log::alert('inside get_email_from_token');
    $this->log::alert(json_encode($res));

    $contents = json_decode($res->contents);
    if (property_exists($contents, "success")) {
      $user_email = $contents->success->email;
    }

    return $user_email;
  }

  private function headers_busqueda_orden(){
    $res = new \stdClass;
$hdr = new \stdClass;
$hdr->name = "id_orden";
$hdr->label = "Codigo Orden Captura";
$res->headers[] = $hdr;
$hdr = new \stdClass;
$hdr->name = "id_expediente";
$hdr->label = "Codigo Expediente";
$res->headers[] = $hdr;
$hdr = new \stdClass;
$hdr->name = "fecha_creacion";
$hdr->label = "Fecha Creacion";
$res->headers[] = $hdr;
$hdr = new \stdClass;
$hdr->name = "estado";
$hdr->label = "Estado";
$res->headers[] = $hdr;
return $res->headers;
  }

  private function numero_expediente($orden) {
    $unidad_arr;
    $idexp = $orden->id_expediente;

    $expediente = Expediente::find($idexp);
    $tipo = $expediente->numero_expediente;
    //echo $tipo;
      return $tipo;
  }

  private function rows_busqueda_orden($arr) {
    $res = new \stdClass;
    $row = new \stdClass;

     if(!is_null($arr["id_expediente"])){
      $expediente = $arr["id_expediente"];
      $tipo_expediente;

      if (preg_match('/PJ/', $expediente)) {
        $temp = ExpedientePJ::where('numero_expediente', $expediente)->first();
        $id = $temp->id;
        $temp2 = ExpedientePJ::find($id);
        $tipo_expediente = $temp2->institucion()->first()->id;
      }
      if (preg_match('/MP/', $expediente)) {
        $temp = ExpedienteMP::where('numero_expediente', $expediente)->first();
        $id = $temp->id;
        $temp2 = ExpedienteMP::find($id);
        $tipo_expediente = $temp2->institucion()->first()->id;
      }
      if (preg_match('/SS/', $expediente)) {
        $temp = ExpedienteSS::where('numero_expediente', $expediente)->first();
        $id = $temp->id;
        $temp2 = ExpedienteSS::find($id);
        $tipo_expediente = $temp2->institucion()->first()->id;
      }
      if (preg_match('/RII/', $expediente)) {
        $temp = Expediente::where('numero_expediente', $expediente)->first();
        $tipo_expediente = $temp->id;
      }
      if(is_null($tipo_expediente)) {$n=0;}
      $orden = OrdenCaptura::where('id_expediente', $tipo_expediente)->get();
      foreach ($orden as $dmp) {
        $row = new \stdClass;
        $row->id_orden = $dmp->id;
        $row->numero_expediente = $this->numero_expediente($dmp);
        $row->fecha = date('Y/m/d',strtotime($dmp->fecha));
        $row->estado = $dmp->estado;
        $row->updated_at = date('Y/m/d',strtotime($dmp->updated_at));
        $res->rows[] = $row;
        return $res->rows;
      }
    }

     if(!is_null($arr["id_imputado"])){
      //echo "la vie e belle";
      $persona = PersonaNatural::find($arr["id_imputado"]);
      if(is_null($persona)) {$n=0;}
       $ordenxpersona = OrdenCapturaPersonaNatural::where('id_persona', $arr["id_imputado"])->get();
       if(is_null($ordenxpersona)) {$n=0;}
       foreach ($ordenxpersona as $dmp) {
         $ordenpersona = $dmp->orden_captura()->first();
         if(is_null($ordenpersona)) {$n=0;}
         $orden = $ordenpersona->orden()->first();
         if(is_null($orden)) {$n=0;}
         $row = new \stdClass;
         $row->id_orden = $orden->id;
         $row->numero_expediente = $this->numero_expediente($orden);
         $row->fecha = date('Y/m/d',strtotime($orden->fecha));
         $row->estado = $orden->estado;
         $row->updated_at = date('Y/m/d',strtotime($orden->updated_at));
         $res->rows[] = $row;
         return $res->rows;
       }
     }

     if(!is_null($arr["nombre_imputado"])){
       //echo "la vie e belle";
       $persona = PersonaNatural::where('nombres', $arr["nombre_imputado"])->get();
       if(is_null($persona)) {$n=0;}
       foreach ($persona as $dmp) {
         $id_persona = $dmp->id;
        $ordenxpersona = OrdenCapturaPersonaNatural::where('id_persona', $id_persona)->get();
        if(is_null($ordenxpersona)) {$n=0;}
        foreach ($ordenxpersona as $dmps) {
          $ordenpersona = $dmps->orden_captura()->first();
          if(is_null($ordenpersona)) {$n=0;}
          $orden = $ordenpersona->orden()->first();
          if(is_null($orden)) {$n=0;}
          $row = new \stdClass;
          $row->id_orden = $orden->id;
          $row->numero_expediente = $this->numero_expediente($orden);
          $row->fecha = date('Y/m/d',strtotime($orden->fecha));
          $row->estado = $orden->estado;
          $row->updated_at = date('Y/m/d',strtotime($orden->updated_at));
          $res->rows[] = $row;
          return $res->rows;
          }
        }
      }

     if(!is_null($arr["delito"])){
      $delito = OrdenCapturaDelito::where('delito', $arr["delito"])->get();
       if(is_null($delito)) {$n=0;}
       foreach ($delito as $dmp) {
         $orden = $dmp->orden_captura()->first();
         if(is_null($orden)) {$n=0;}
         $row = new \stdClass;
         $row->id_orden = $orden->id;
         $row->numero_expediente = $this->numero_expediente($orden);
         $row->fecha = date('Y/m/d',strtotime($orden->fecha));
         $row->estado = $orden->estado;
         $row->updated_at = date('Y/m/d',strtotime($orden->updated_at));
         $res->rows[] = $row;
         return $res->rows;
       }
     }

     //if(!is_null($arr["año"])){}

  }

  public function busqueda_orden($arr) {
    $res = new \stdClass;
    $res->headers = $this->headers_busqueda_orden();
    $res->rows = $this->rows_busqueda_orden($arr);
    return $res;
  }

}
