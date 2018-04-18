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
use App\Imputado;
use App\Expediente;
use App\ExpedientePJ;
use App\Rol;
use App\PersonaNatural;
use App\Persona;
use App\Dependencia;
use App\Institucion;

class CatalogoTools
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

  private function headers_expediente_pj(){
    $res = new \stdClass;
    $hdr = new \stdClass;
    $hdr->name = "id_expediente";
    $hdr->label = "Codigo Expediente";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "id_pj_expediente";
    $hdr->label = "Numero Expediente PJ";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "id_rii_expediente";
    $hdr->label = "Numero Expediente RII";
    $res->headers[] = $hdr;
    return $res->headers;
  }

  private function pj_id_expediente($proceso_judicial_id){
    //inicializamos el proceso judicial
    $proceso_judicial = ProcesoJudicial::find($proceso_judicial_id);
    if (!is_null($proceso_judicial)) {
      //buscamos el expediente PJ
      $expedientePJ = $proceso_judicial->expediente()->first();
      echo $expedientePJ;
      $es = $WE;
      if (!is_null($expedientePJ)) {
        //retornamos el numero expediente PJ
        return $expedientePJ->numero_expediente;
      }
    }
  }

  private function rii_id_expediente($proceso_judicial_id){
    //inicializamos el proceso judicial
    $proceso_judicial = ProcesoJudicial::find($proceso_judicial_id);
    if (!is_null($proceso_judicial)) {
      //buscamos el expediente PJ
      $expedientePJ = $proceso_judicial->expediente()->first();
      if (!is_null($expedientePJ)) {
        //buscamos el expediente
        $expediente = $expedientePJ->institucion()->first();
        if (!is_null($expediente)) {
          //retornamos el numero expediente RII
          return $expediente->numero_expediente;
        }
      }
    }
  }

  private function id_expediente($proceso_judicial_id){
    //inicializamos el proceso judicial
    $proceso_judicial = ProcesoJudicial::find($proceso_judicial_id);
    //echo $proceso_judicial_id;
    if (!is_null($proceso_judicial)) {
      //buscamos el expediente PJ
      $expedientePJ = $proceso_judicial->expediente()->first();
      if (!is_null($expedientePJ)) {
        //buscamos el expediente
        $expediente = $expedientePJ->institucion()->first();
        if (!is_null($expediente)) {
          //retornamos el id expediente
          return $expediente->id;
        }
      }
    }
  }

  private function rows_expediente_pj($arr) {
    $res = new \stdClass;
    //buscamos el juez
    $juez = Juez::find($arr["id_juez"]);
    if (is_null($juez)) {return 0;}
    //buscamos todos los procesos que el juez tenga asignado
    $asignaciones = $juez->asignaciones()->get();
    //echo $asignaciones;
    if (is_null($asignaciones)) {return "juez sin procesos asignados";}

    foreach ($asignaciones as $asig) {
      $row = new \stdClass;
      //$row->id_expediente = $this->id_expediente($asig->proceso_judicial_id);
      $row->id_pj_expediente = $this->pj_id_expediente($asig->proceso_judicial_id);
      //$row->id_rii_expediente = $this->rii_id_expediente($asig->proceso_judicial_id);
      $res->rows[] = $row;
    }
    return $res->rows;
  }

  public function expediente_pj_list($arr) {
    $res = new \stdClass;
    $res->headers = $this->headers_expediente_pj();
    $res->rows = $this->rows_expediente_pj($arr);
    return $res;
  }

  private function headers_expedientepj(){
    $res = new \stdClass;
    $hdr = new \stdClass;
    $hdr->name = "id_expediente";
    $hdr->label = "Codigo Expediente";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "numero_expediente";
    $hdr->label = "Numero Expediente PJ";
    $res->headers[] = $hdr;
    return $res->headers;
  }

  private function rows_expedientepj($token) {
    $n = 0;
    $res = new \stdClass;
    //$res->rows[]=[]; this fails is no data is returned...
    foreach (ExpedientePJ::All() as $dmp) {
      if ($n != 10) {
      //if (preg_match('/PJ/', $dmp->numero_expediente)) {
      if ($dmp->id > 146) {
      $row = new \stdClass;
      $row->id_expediente_pj = $dmp->id;
      $row->numero_expediente = $dmp->numero_expediente;
      $res->rows[] = $row;
      $n = $n + 1;
          }
    }
    }
    return $res->rows;
  }

  public function expedientepj_list($arr) {
    $res = new \stdClass;
    $res->headers = $this->headers_expedientepj();
    $res->rows = $this->rows_expedientepj($arr);
    return $res;
  }

  private function imputado($id_denuncia){
   $imputados_arr = [];
    $denuncia = Denuncia::find($id_denuncia);
    $imputados = $denuncia->imputados()->get();
    //echo $imputados;
    foreach ($imputados as $imp) {
      $imputado = new \stdClass;
    $id = $imp->rol()->first()->persona_natural_id;
    $persona = $this->get_persona_natural($id);
    $imputado->id = $persona->id;
    $imputado->nombre = $persona->nombres.' '.$persona->primer_apellido.' '.$persona->segundo_apellido;
    $imputados_arr[] = $imputado;
    unset($imputado);
    }
    return $imputados_arr;

  }

  private function rows_expediente_imputado($arr) {
    $res = new \stdClass;
    $n = 0;
    //buscamos el juez
    $expediente_id = $arr["id_expediente"];
    $expediente_pj = ExpedientePJ::find($expediente_id);
    $expediente = $expediente_pj->institucion()->first()->id;
    //buscamos el documento denuncia
    $documento = Documento::where("expediente_id", $expediente)->get();
    foreach ($documento as $dmp) {
      if (preg_match('/Denuncia/', $dmp->documentable_type)) {
      $row = new \stdClass;
      $row->imputado = $this->imputado($dmp->documentable_id);
      $res->rows = $row;
      $n = $n + 1;
        }
          }
    if($n > 0)
    {return $res->rows;}
    return 0;
  }

  public function expediente_imputado($arr) {
    $res = new \stdClass;
    //$res->headers = $this->headers_expediente_imputado();
    $res->rows = $this->rows_expediente_imputado($arr);
    return $res;
  }

  private function victimas($id_denuncia){
   $imputados_arr = [];
    $denuncia = Denuncia::find($id_denuncia);
    $imputados = $denuncia->victimas()->get();
    foreach ($imputados as $imp) {
      $imputado = new \stdClass;
    $id = $imp->rol()->first()->persona_natural_id;
    $persona = $this->get_persona_natural($id);
    $imputado->id = $persona->id;
    $imputado->nombre = $persona->nombres.' '.$persona->primer_apellido.' '.$persona->segundo_apellido;
    $imputados_arr[] = $imputado;
    unset($imputado);
    }
    return $imputados_arr;

  }

  private function ofendidos($id_denuncia){
   $imputados_arr = [];
    $denuncia = Denuncia::find($id_denuncia);
    $imputados = $denuncia->ofendidos()->get();
    foreach ($imputados as $imp) {
      $imputado = new \stdClass;
    $id = $imp->rol()->first()->persona_natural_id;
    $persona = $this->get_persona_natural($id);
    $imputado->id = $persona->id;
    $imputado->nombre = $persona->nombres.' '.$persona->primer_apellido.' '.$persona->segundo_apellido;
    $imputados_arr[] = $imputado;
    unset($imputado);
    }
    return $imputados_arr;

  }

  private function rows_expediente_victima($arr) {
    $res = new \stdClass;
    $n = 0;
    //buscamos el juez
    $expediente_id = $arr["id_expediente"];
    $expediente_pj = ExpedientePJ::find($expediente_id);
    $expediente = $expediente_pj->institucion()->first()->id;
    if (is_null($expediente)) {return 0;}
    //buscamos el documento denuncia
    $documento = Documento::where("expediente_id", $expediente)->get();
    foreach ($documento as $dmp) {
      if (preg_match('/Denuncia/', $dmp->documentable_type)) {
      $row = new \stdClass;
      $row->victimas = $this->victimas($dmp->documentable_id);
      $row->ofendidos = $this->ofendidos($dmp->documentable_id);
      $res->rows = $row;
      $n = $n + 1;
        }
          }
      if($n > 0)
      {return $res->rows;}
      return 0;
  }

  public function expediente_victima($arr) {
    $res = new \stdClass;
    //$res->headers = $this->headers_expediente_imputado();
    $res->rows = $this->rows_expediente_victima($arr);
    return $res;
  }
}
