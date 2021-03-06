<?php

namespace App;

use App\Captura;
use App\Flagrancia;
use App\CapturaFinExtradicion;
use App\Detenido;
use App\MenorDetenido;
use App\Workflow;
use Validator;
use App\Passport;
use App\Funcionario;
use App\FuncionarioSS;
use App\Lugar;
use App\LugarSS;
use App\Rol;
use App\PersonaNatural;
use App\Persona;
use App\Dependencia;
use App\Institucion;

class RealizarCapturaTools
{

  private $captura_required;
  private $log;
  private $workflow_type;

  public function __construct()
  {
      $this->log = new  \Log;
      $this->workflow_type = "captura_realizada";
      //$this->captura_required =  Array("nombres", "primer_apellido", "segundo_apellido", "fecha_nacimiento", "sexo", "genero", "id_lugar", "id_funcionario", "id_unidad", "descripcion_captura", "fecha_captura");
  }

  private function get_persona_natural($id) {
    $persona_natural = PersonaNatural::find($id);
    //if (is_null($persona_natural)) {return null;}
    return $persona_natural;
  }

  private function detenido($id) {
    $detenidos_arr = [] ;
    $captura = Captura::find($id) ;
    //$detenido = new \stdClass;
    $detenidos = $captura->detenidos()->get();
    if (is_null($detenidos)) {return $detenidos_arr;}
    foreach($detenidos as $d) {
      $detenido = new \stdClass;
      $persona_natural_id = $d->rol()->first()->persona_natural_id;
      if (is_null($persona_natural_id)) {return null;}
      $persona = $this->get_persona_natural($persona_natural_id);

      $detenido->numero_detenido = $d->id;
       //cambio, eliminar estos datos de detenido, no los necesita porque estan en la orden de captura
      //$detenido->id_orden = $d->id_orden;
      //$detenido->id_requerimiento = $d->id_requerimiento;
      //$detenido->id_expediente = $d->id_expediente;
      $detenido->numero_captura = $d->id_captura;

      $detenido->identidad = $persona->id;

      if (isset($persona)) {
        $detenido->nombres = $persona->nombres;
        $detenido->apellidos = $persona->primer_apellido.', '.$persona->segundo_apellido;
        //$detenido->segundo_apellido = $persona_natural_id;
      }

      $detenido->fecha_nacimiento = $d->fecha_nacimiento;
      $detenido->nacionalidad = $d->nacionalidad;
      $detenido->genero = $d->genero;
      $detenido->sexo = $d->sexo;
      $detenido->edad = $d->edad;

      if (preg_match('/MenorDetenido/',$d->tipoable_type))
      {
        $menordetenidos = $d->tipoable_id;
        $tempmenor = MenorDetenido::find($menordetenidos);
        if (is_null($tempmenor)) {return null;}
        $menor = $tempmenor;
        $idapoderado = $menor->apoderado;
        $detenido->apoderado = $this->apoderado($idapoderado);

        //$detenidos_arr[] = $detenido;

      }

         $idunidad = $d->lugar_retencion;
         $detenido->lugar_retencion = $this->unidad($idunidad);
         $detenidos_arr[] = $detenido;
         unset($detenido);
          //break;
    }

    return $detenidos_arr;
  }

  private function lugar_captura($id) {
      $lugarss_arr;
      $captura = Captura::find($id);
      $lugar_captura = new \stdClass;
      $idlugar = $captura->id_lugar;
      $lugar_ss = LugarSS::find($idlugar);
      //$s = Lugar::institucionable($lugar_ss);

      if (is_null($lugar_ss)) {return $lugarss_arr;}

        $lugar_captura->regional_id = $lugar_ss->regional_id;
        $lugar_captura->departamento_id = $lugar_ss->departamento_id;
        $lugar_captura->municipio_id = $lugar_ss->municipio_id;
        $lugar_captura->aldea_id = $lugar_ss->aldea_ss_id;
        $lugar_captura->colonia_id = $lugar_ss->colonia_ss_id;
        $lugar_captura->ciudad_id = $lugar_ss->ciudad_ss_id;
        $lugar_captura->sector_id = $lugar_ss->sector_ss_id;
        //$lugar_captura->descripcion = $lugar->descripcion;
        //$lugar_captura->caracteristicas = $lugar->caracteristicas;

        $lugarss_arr = $lugar_captura;
        return $lugarss_arr;
  }

  private function funcionario($id) {
      $funcionarios_arr = [];
      $captura = Captura::find($id);
      $detenidos = $captura->detenidos()->get()->first();
      $responsable = new \stdClass;
      $funcionarioss = $captura->id_funcionario;
      //$investigadores = $captura->id_funcionario;

      //funcionarios
      $funcionarios_ss = FuncionarioSS::find($funcionarioss);
      if (is_null($funcionarios_ss)) {return null;}
        $funcionario = $funcionarios_ss->institucion()->first();
        if (is_null($funcionario)) {return null;}
        $funcionarioid = $funcionario->id;
        $funcionario_policia = Funcionario::find($funcionarioid);
        $rol_funcionario = $funcionario_policia->rol()->first();
        $persona_natural_id = $rol_funcionario->persona_natural_id;
        $persona = $this->get_persona_natural($persona_natural_id);
        if (is_null($persona)) {return null;}

        //funcionario
        $responsable->nombres = $persona->nombres;
        $responsable->apellidos = $persona->primer_apellido.', '.$persona->segundo_apellido;
        $responsable->placa = $funcionarios_ss->placa;
        $funcionarios_arr[] = $responsable;
        return $funcionarios_arr;
  }

  private function unidad($id) {
      $unidad_arr = [];
      $unidad = Dependencia::find($id);
      if (is_null($unidad)) {return null;}
      $unidades = new \stdClass;
      $unidades->id_unidad = $unidad->institucion_id;
      $unidades->nombre = $unidad->nombre;

        $unidad_arr[] = $unidades;
        return $unidad_arr;
  }

  private function apoderado($id) {
      $apoderado_arr = [];
      $apoderado = new \stdClass;
      if (is_null($id)) {
        $apoderado_arr[] = $apoderado;
        return $apoderado_arr;
      }
      $persona = $this->get_persona_natural($id);
      if (is_null($persona)) {
        $apoderado_arr[] = $apoderado;
        return $apoderado_arr;
      }
      $apoderado->identidad = $persona->id;
      $apoderado->nombres = $persona->nombres;
      $apoderado->apellidos = $persona->primer_apellido.', '.$persona->segundo_apellido;


      $apoderado_arr[] = $apoderado;
      return $apoderado_arr;
  }

  private function tipo_captura($id) {
    $tipo_captura_arr;
      $captura = Captura::find($id);
      if (is_null($captura)) { return json_encode($res); }

      $tipo_captura = new \stdClass;

      $id_capturable = $captura->capturable_id;
      if (is_null($captura->capturable_id))
      {
        $tipo_captura->orden_captura = 1;
        $tipo_captura->id_captura = $captura->id;
        $tipo_captura->flagrancia = 0;
        $tipo_captura->captura_fin_extradicion = 0;
        $tipo_captura_arr = $tipo_captura;
        return $tipo_captura_arr;
      }

      $this->log::alert(json_encode($captura));
      if (preg_match('/Flagrancia/',$captura->capturable_type))
      {
        $flagrancia = Flagrancia::find($id_capturable);

        $tipo_captura->orden_captura = 0;
        $tipo_captura->flagrancia = 1;
        $tipo_captura->id_flagrancia = $flagrancia->id;
        $tipo_captura->id_denuncia = $flagrancia->id_denuncia;
        $tipo_captura->captura_fin_extradicion = 0;
        $tipo_captura_arr = $tipo_captura;
        return $tipo_captura_arr;

      }
      if (preg_match('/FinExtradicion/',$captura->capturable_type))
      {
        $extradicion = CapturaFinExtradicion::find($id_capturable);

        $tipo_captura->orden_captura = 0;
        $tipo_captura->flagrancia = 0;
        $tipo_captura->captura_fin_extradicion = 1;
        $tipo_captura->id_captura_extradicion = $extradicion->id;
        $tipo_captura->id_nota_roja = $extradicion->id_nota_roja;
        $tipo_captura_arr = $tipo_captura;
        return $tipo_captura_arr;
      }
  }

  private function captura($id) {
    $captura_arr;
    $captura = Captura::find($id);
    $capturas = new \stdClass;

    $capturas->numero_captura = $captura->id;
    $capturas->id_orden_captura = $captura->id_orden;
    $capturas->id_requerimiento = $captura->id_requerimiento;
    $capturas->id_expediente = $captura->id_expediente;
    //date('Y/m/d',strtotime($dss->updated_at))
    $capturas->fecha_captura = date('Y/m/d',strtotime($captura->fecha_captura));
    $capturas->descripcion_captura = $captura->descripcion_captura;
    $capturas->observaciones = $captura->observaciones;

      $captura_arr = $capturas;
    return $captura_arr;
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

  private function get_captura($captura, $id, $token) {
    $captura_arr = [];
    //$user_email = $this->get_email_from_token($token);
    //if (!isset($user_email)) { return $captura_arr; }
    //if (empty($user_email)) {return $captura_arr; }

    $capturas = new \stdClass;
    //$capturas = array($captura);
    //$arr = $this->lugar_captura($capturas, $id);
    //$capturas->captura = $captura;

    $capturas->persona_capturada = $this->detenido($id);
    $capturas->lugar_captura = $this->lugar_captura($id);
    $capturas->capturado_por = $this->funcionario($id);
    $capturas->tipo_captura = $this->tipo_captura($id);
    $capturas->captura = $this->captura($id);
    $captura_arr[] = $capturas;
    return $captura_arr;
  }

  public function ss_captura($captura_id, $token){
    //$captura = Captura::find($captura_id);
    //return $captura;
    $res = new \stdClass;
    //echo $captura_id;
    $captura = Captura::find($captura_id);
    if (is_null($captura)) { return json_encode($res); }
    $id = $captura->id;
    //echo $id;
    //$this->log::alert(json_encode($captura));

    $result = $this->get_captura($captura, $id, $token);

    if (!isset($result)) { return json_encode($res); }
    if (empty($result)) { return json_encode($res); }

    //$res->ss_captura = $result[0];
    $json_result = json_encode($result);
    return $json_result;

  }

  public function ss_capturas($captura_id, $token){

    $persona_natural = Captura::find($captura_id);
    return $persona_natural;
  }

  private function headers(){
    $res = new \stdClass;
    $hdr = new \stdClass;
    $hdr->name = "id_captura";
    $hdr->label = "Numero Captura";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "id_lugar";
    $hdr->label = "Departamento captura";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "fecha_captura";
    $hdr->label = "Fecha Captura";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "tipo_captura";
    $hdr->label = "Tipo Captura";
    $res->headers[] = $hdr;
    return $res->headers;
  }

  private function deptocaptura($captura){
    //$captura = Captura::find($id);
    //$lugar_captura = new \stdClass;
    $idlugar = $captura->id_lugar;
    $lugar_ss = LugarSS::find($idlugar);
    $depto = "";
    if (is_null($lugar_ss)) {return $depto;}
     $depto = $lugar_ss->departamento_id;
     return $depto;
  }

  private function tipocaptura($captura){
    $tipo_captura = "Orden Judicial";

    if (preg_match('/Flagrancia/',$captura->capturable_type))
    { $tipo_captura = "Captura en Flagrancia";
      return $tipo_captura;
    }

    if (preg_match('/FinExtradicion/',$captura->capturable_type))
    { $tipo_captura = "Captura Fines Extradicion";
      return $tipo_captura;
    }
     return $tipo_captura;
  }

  private function tipoworkflow($captura){
    $id_capturable = $captura->capturable_id;

    if (preg_match('/Flagrancia/',$captura->capturable_type))
    {
      $flagrancia = Flagrancia::find($id_capturable);
      $workflow = $flagrancia->workflow_state;
      return $workflow;
    }

    if (preg_match('/FinExtradicion/',$captura->capturable_type))
    {
      //$tipo_captura = "Captura Fines Extradicion";
      $extradicion = CapturaFinExtradicion::find($id_capturable);
      $workflow = $extradicion->workflow_state;
      return $workflow;
    }
      $workflow = $captura->workflow_state;
      return $workflow;
  }

  private function rows($token) {
    $res = new \stdClass;
    //$res->rows[]=[]; this fails is no data is returned...
    foreach (Captura::All() as $dmp) {
      $row = new \stdClass;
      $row->numero_captura = $dmp->id;
      $row->departamento_captura = $this->deptocaptura($dmp);
      $row->fecha_captura = date('Y/m/d',strtotime($dmp->fecha_citacion));
      $row->tipo_captura = $this->tipocaptura($dmp);
      $row->updated_at = date('Y/m/d',strtotime($dmp->updated_at));
      $res->rows[] = $row;
    }
    try {
      return $res->rows;
    } catch (\Exception $e) {
      return null;
    }
  }

  public function ss_list_captura($token) {
    $res = new \stdClass;
    $res->headers = $this->headers();
    $res->rows = $this->rows($token);
    return $res ;
  }
}
