<?php

namespace App;

use App\Expediente;
use App\ExpedientePJ;
use App\Workflow;
use Validator;
use App\Passport;
use App\Funcionario;
use App\FuncionarioPJ;
use App\FuncionarioMP;
use App\Lugar;
use App\LugarPJ;
use App\Rol;
use App\PersonaNatural;
use App\Persona;
use App\Dependencia;
use App\Institucion;
use App\Notificacion;
use App\ImputadoNotificacion;
use App\TestigoNotificacion;
use App\VictimaNotificacion;
use App\DelitoNotificacion;
use App\Imputado;
use App\Testigo;
use App\Victima;
use App\SujetoProcesalNotificacion;
use App\CanalEnvioCN;
use App\Delito;
use App\Juez;
use App\Fiscal;
use App\ProcesoJudicial;
use App\AsignacionJuez;

class NotificacionTools
{

  private $solicitud_required;
  private $log;
  private $workflow_type;

  public function __construct()
  {
      $this->log = new  \Log;
      $this->workflow_type = "solicitud_realizada";
  }

  private function get_persona_natural($id) {
    $persona_natural = PersonaNatural::find($id);
    if (is_null($persona_natural)) {return null;}
    return $persona_natural;
  }

  private function audiencia($id) {
      $audiencia;
      $catalogo = DetalleListaValorRelacional::find($id);
      if (is_null($catalogo)) {return null;}
      $audiencia = $catalogo->valor;

        return $audiencia;
  }

  private function etapa($id) {
      $etapa;
      $catalogo = DetalleListaValorRelacional::find($id);
      if (is_null($catalogo)) {return null;}
      $etapa = $catalogo->valor;

        return $etapa;
  }

  private function notificaciones($id) {
    $notificacion_arr;
    //$tipo  "App\SolicitudOrdenCaptura";
    $notificacion = Notificacion::find($id);
    $notificaciones = new \stdClass;

    $notificaciones->id_notificacion = $notificacion->id;
    $notificaciones->organo_juridiccional = $notificacion->organo_juridiccional;
    $notificaciones->fecha_creacion = date('Y/m/d',strtotime($notificacion->fecha_creacion));
    $notificaciones->audiencia = $this->audiencia($notificacion->audiencia);
    $notificaciones->etapa = $this->etapa($notificacion->etapa);
    $notificaciones->asunto = $notificacion->asunto;
    $notificaciones->objeto_proceso = $notificacion->objeto_proceso;
    $notificaciones->observaciones = $notificacion->observaciones;

    $notificacion_arr = $notificaciones;
    return $notificacion_arr;
  }

  private function numero_expediente($id) {
    $notificacion_arr;
    //$tipo  "App\SolicitudOrdenCaptura";
    $notificacion = Notificacion::find($id);
    $notificaciones = new \stdClass;

    $expediente = $notificacion->expediente()->first();
    if (is_null($expediente)) { return null; }

    $expedientepj = $expediente->numero_expediente;
    $expedienterii = $expediente->institucion()->first()->numero_expediente;

    //$expediente_pj = $citacion->expediente()->first()->numero_expediente;
    //$expediente_pj = $citacion->expediente()->institucion()->first()->numero_expediente;

    $notificaciones->numero_expedinte_rii = $expedienterii;
    $notificaciones->numero_expedinte_PJ = $expedientepj;

    $notificacion_arr = $notificaciones;
    return $notificacion_arr;
  }

  private function juez($id) {
      $numero_juez = "null";
      $notificacion = Notificacion::find($id);
      $id_juez = $notificacion->id_juez;
      if (is_null($id_juez)) {return $numero_juez;}
      $juez = Juez::find($id_juez);
      if (is_null($juez)) {return $numero_juez;}
      $numero_juez = $juez->codigo;
      return $numero_juez;
  }

  private function fiscal($id) {
      $funcionarios_arr;
      $notificacion = Notificacion::find($id);
      $idfiscal = $notificacion->id_fiscal;
      $responsable = new \stdClass;
      $fiscal = Fiscal::find($idfiscal);

        $rol_funcionario = $fiscal->rol()->first();
        $persona_natural_id = $rol_funcionario->persona_natural_id;
        $persona = $this->get_persona_natural($persona_natural_id);

        //funcionario
        $responsable->nombres = $persona->nombres;
        $responsable->apellidos = $persona->primer_apellido.', '.$persona->segundo_apellido;
        $funcionarios_arr = $responsable;
        return $funcionarios_arr;
  }

  private function citado($id) {
    $citacion_arr;
    //$tipo  "App\SolicitudOrdenCaptura";
    $citacion = Citacion::find($id);
    $citaciones = new \stdClass;

    $citaciones->persona_citada = $citacion->persona_natural;
    $citaciones->tipo = $citacion->tipo;

    $citacion_arr = $citaciones;
    return $citacion_arr;
  }

  private function medios_notificacion($id) {
      $notificacion_arr = [];
      $notificacion = Notificacion::find($id);
      //echo $notificacion;
      $canales_envio = $notificacion->canales_envio()->get();
      //$delitos = $orden_captura->delitos()->get();
      if (is_null($canales_envio)) {return null;}
      foreach($canales_envio as $cn) {
        $canal = new \stdClass;

        $canal->id = $cn->id;
        $canal->canal_envio = $cn->canal_envio;
        $canal->medios_envio = $cn->medios_envio;
        $canal->observaciones = $cn->observaciones;

        $notificacion_arr[] = $canal;
        unset($canal);
      }
      return $notificacion_arr;
  }

  private function imputados($id) {
      $imputados_arr = [];
      $notificacion = Notificacion::find($id);
      //echo $notificacion;
      $imputados = $notificacion->imputados()->get();
      //echo $imputados;
      //$delitos = $orden_captura->delitos()->get();
      if (is_null($imputados)) {return null;}
      foreach($imputados as $cn) {
        $imputado = new \stdClass;

        $impu = Imputado::find($cn->id_imputado);
        if (is_null($impu)) {}
          //echo $impu;
          $persona_natural_id = $impu->rol()->first()->persona_natural_id;
          if (is_null($persona_natural_id)) {}

          $persona = $this->get_persona_natural($persona_natural_id);
          if (is_null($persona)) {
            $imputado->id = $cn->id;
            $imputado->id_persona_natural = $persona_natural_id;
            $imputado->nombres = "persona NO encontrada";

            $imputados_arr[] = $imputado;
            unset($imputado);
          }

          $imputado->id = $cn->id;
          $imputado->id_persona_natural = $persona_natural_id;
          $imputado->nombres = $persona->nombres;
          $imputado->apellidos = $persona->primer_apellido.', '.$persona->segundo_apellido;

          $imputados_arr[] = $imputado;
          unset($imputado);

      }
      return $imputados_arr;
  }

  private function victimas($id) {
      $victimas_arr = [];
      $notificacion = Notificacion::find($id);
      //echo $notificacion;
      $victimas = $notificacion->victimas()->get();
      //$delitos = $orden_captura->delitos()->get();
      if (is_null($victimas)) {return null;}
      foreach($victimas as $cn) {
        $victima = new \stdClass;

        $victi = Victima::find($cn->id_victima);
        if (is_null($victi)) {}

          $persona_natural_id = $victi->rol()->first()->persona_natural_id;
          if (is_null($persona_natural_id)) {}

          $persona = $this->get_persona_natural($persona_natural_id);
          if (is_null($persona)) {
            $victima->id = $cn->id;
            $victima->id_persona_natural = $persona_natural_id;
            $victima->nombres = "persona NO encontrada";

            $victimas_arr[] = $victima;
            unset($victima);
          }

          $victima->id = $cn->id;
          $victima->id_persona_natural = $persona_natural_id;
          $victima->nombres = $persona->nombres;
          $victima->apellidos = $persona->primer_apellido.', '.$persona->segundo_apellido;

          $victimas_arr[] = $victima;
          unset($victima);

      }
      return $victimas_arr;
  }

  private function testigos($id) {
      $testigos_arr = [];
      $notificacion = Notificacion::find($id);
      //echo $notificacion;
      $testigos = $notificacion->testigos()->get();
      //$delitos = $orden_captura->delitos()->get();
      if (is_null($testigos)) {return null;}
      foreach($testigos as $cn) {
        $testigo = new \stdClass;

        $testi = Testigo::find($cn->id_testigo);
        if (is_null($testi)) {}

          $persona_natural_id = $testi->rol()->first()->persona_natural_id;
          if (is_null($persona_natural_id)) {}

          $persona = $this->get_persona_natural($persona_natural_id);
          if (is_null($persona)) {
            $testigo->id = $cn->id;
            $testigo->id_persona_natural = $persona_natural_id;
            $testigo->nombres = "persona NO encontrada";

            $testigos_arr[] = $testigo;
            unset($testigo);
          }

          $testigo->id = $cn->id;
          $testigo->id_persona_natural = $persona_natural_id;
          $testigo->nombres = $persona->nombres;
          $testigo->apellidos = $persona->primer_apellido.', '.$persona->segundo_apellido;

          $testigos_arr[] = $testigo;
          unset($testigo);

      }
      return $testigos_arr;
  }

  private function delitos($id) {
      $delitos_arr = [];
      $notificacion = Notificacion::find($id);
      //echo $notificacion;
      $delitos = $notificacion->delitos()->get();
      //$delitos = $orden_captura->delitos()->get();
      if (is_null($delitos)) {return null;}
      foreach($delitos as $cn) {
        $delito = new \stdClass;

        //$deli = DelitoNotificacion::find($cn->id_delito);
        //if (is_null($deli)) {}

          $tempdelito = Delito::find($cn->id_delito);
          if (is_null($tempdelito)) {
            $delito->delito = "Delito No Encontrado";
          }

          $delito->id = $tempdelito->id;
          $delito->delito = $tempdelito->descripcion;

          $delitos_arr[] = $delito;
          unset($delito);

      }
      return $delitos_arr;
  }

  private function actores_procesales($id) {
      $sujetos_arr = [];
      $notificacion = Notificacion::find($id);
      //echo $notificacion;
      $sujetos_procesales = $notificacion->sujetos_procesales()->get();
      //$delitos = $orden_captura->delitos()->get();
      if (is_null($sujetos_procesales)) {return null;}
      foreach($sujetos_procesales as $cn) {
        $sujeto = new \stdClass;

        $sujeto->nombre = $cn->nombre;
        $sujeto->tipo = $cn->tipo;

        $sujetos_arr[] = $sujeto;
        unset($sujeto);
      }
      return $sujetos_arr;
  }

  private function partes($id) {
      $notificacion_arr = [];
      $notificacion = Notificacion::find($id);
      $actores = new \stdClass;

        $actores->actores_procesales = $this->actores_procesales($id);
        $actores->imputados = $this->imputados($id);
        $actores->victimas = $this->victimas($id);
        $actores->testigos = $this->testigos($id);

        $notificacion_arr[] = $actores;
      return $notificacion_arr;
  }

  private function funcionario_pj($id) {
      $funcionarios_arr = [];
      $notificacion = Notificacion::find($id);
      $responsable = new \stdClass;
      $funcionariopj = $notificacion->id_funcionario;
      //$investigadores = $captura->id_funcionario;

      //funcionarios
      $funcionarios_pj = FuncionarioPJ::find($funcionariopj);
      if (is_null($funcionarios_pj)) {return null;}
        $funcionario = $funcionarios_pj->institucion()->first();
        $funcionarioid = $funcionario->id;
        $funcionario_secretario = Funcionario::find($funcionarioid);
        if (is_null($funcionario_secretario)) {return null;}
        $rol_funcionario = $funcionario_secretario->rol()->first();
        $persona_natural_id = $rol_funcionario->persona_natural_id;
        $persona = $this->get_persona_natural($persona_natural_id);
        if (is_null($persona)) {return null;}
        //funcionario
        $responsable->nombres = $persona->nombres;
        $responsable->apellidos = $persona->primer_apellido.', '.$persona->segundo_apellido;
        $funcionarios_arr[] = $responsable;
        return $funcionarios_arr;
  }

  private function tipo_identidad($persona) {
    $persona_tipo_identidad = strtolower($persona->identificable_type);
    $tipo_identidad = "portador";

    if (preg_match('/anonimo/', $persona_tipo_identidad)) {
      $tipo_identidad = "anonimo";
    }

    if (preg_match('/desconocido/', $persona_tipo_identidad)) {
      $tipo_identidad = "desconocido";
    }

    if (preg_match('/protegido/', $persona_tipo_identidad)) {
      $tipo_identidad = "protegido";
    }

    if (preg_match('/noportador/', $persona_tipo_identidad)) {
      $tipo_identidad = "no_portador";
    }

    if (preg_match('/deoficio/', $persona_tipo_identidad)) {
      $tipo_identidad = "de_oficio";
    }

    return $tipo_identidad;
  }

  private function workflow_actions($solicitud_tipo, $user_email) {
    $actions_arr = [];
    $solicitudes = $solicitud_tipo->solicitud()->first();

    if (is_null($solicitudes)) {return $actions_arr; }

    $wf = new Workflow;
    $params = new \stdClass;
    $params->subject_id = $solicitudes->id;
    $params->object_id = $solicitudes->id;
    $params->workflow_type = "realizar_solicitud";  //$this->workflow_type;
    $params->user_email = $user_email;
    $this->log::alert("json_encoded w/o True parameter");
    $this->log::alert(json_encode($params));
    // $this->log::alert("json_encoded with True parameter");
    // $this->log::alert(json_encode($params),true );

    // watch this line
    $actions = $wf->user_actions(json_encode($params));
    $this->log::alert(json_encode($actions));

    if (is_null($actions)) {return $actions_arr; }
    if (!property_exists($actions, "contents")) { return $actions_arr; }
    if (!property_exists($actions, "code")) { return $actions_arr; }
    if (!$actions->code == 200) { return $actions_arr; }
    $json_actions = json_decode($actions->contents);

    if (!is_null($json_actions)) {
      if (property_exists($json_actions, "message")) {
        $actions_arr = $json_actions->message;
      }
    }

    return $actions_arr;
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

  private function get_notificacion($notificacion, $id, $token) {
    $notificacion_arr;
    //$user_email = $this->get_email_from_token($token);
    //if (!isset($user_email)) { return $notificacion_arr; }
    //if (empty($user_email)) {return $notificacion_arr; }

    $notificaciones = new \stdClass;
    $notificaciones->numero_expediente = $this->numero_expediente($id);
    $notificaciones->numero_juez = $this->juez($id);
    $notificaciones->fiscal = $this->fiscal($id);
    $notificaciones->notificacion = $this->notificaciones($id);
    $notificaciones->delitos = $this->delitos($id);
    $notificaciones->partes_involucradas = $this->partes($id);
    $notificaciones->canales_envio = $this->medios_notificacion($id);
    $notificaciones->creador = $this->funcionario_pj($id);
    $notificacion_arr = $notificaciones;
    return $notificacion_arr;
  }

  public function pj_notificaciones($notificacion_id, $token){
    $res = new \stdClass;
    $notificacion = Notificacion::find($notificacion_id);
    if (is_null($notificacion)) { return json_encode($res); }
    $id = $notificacion->id;

    $result = $this->get_notificacion($notificacion, $id, $token);

    if (!isset($result)) { return json_encode($res); }
    if (empty($result)) { return json_encode($res); }

    $json_result = json_encode($result);
    return $json_result;

  }

  private function headers(){
    $res = new \stdClass;
    $hdr = new \stdClass;
    $hdr->name = "numero_notificacion";
    $hdr->label = "Numero Notificacion";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "numero_expediente";
    $hdr->label = "Numero Expediente";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "organo_juridiccional";
    $hdr->label = "Organo Juridiccional";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "fecha_notificacion";
    $hdr->label = "Fecha Notificacion";
    $res->headers[] = $hdr;
    return $res->headers;
  }

  private function expediente_pj($id) {

    $notificacion = Notificacion::find($id);

    $expediente = $notificacion->expediente()->first();
    if (is_null($expediente)) { return null; }

    $expedientepj = $expediente->numero_expediente;

    return $expedientepj;
  }

  private function rows($token) {
    $res = new \stdClass;
    //$res->rows[]=[]; this fails is no data is returned...
    try {
      $noti = Notificacion::where('notificado', 0)->get();
      foreach ($noti as $dmp) {
        $row = new \stdClass;
        $row->numero_notificacion = $dmp->id;
        $row->numero_expediente = $this->expediente_pj($dmp->id);
        $row->organo_juridiccional = $dmp->organo_juridiccional;
        $row->fecha_notificacion = date('Y/m/d',strtotime($dmp->fecha_notificacion));
        $row->updated_at = date('Y/m/d',strtotime($dmp->updated_at));
        $res->rows[] = $row;
        }
        return $res->rows;

    } catch (\Exception $e) {
         return null;
    }

  }

  public function pj_list_notificaciones($token) {
    $res = new \stdClass;
    $res->headers = $this->headers();
    $res->rows = $this->rows($token);
    return $res ;
  }
}
