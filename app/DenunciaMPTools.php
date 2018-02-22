<?php

namespace App;

use App\DenunciaMP;
use App\DenunciaMPEscrita;
use App\DenunciaMPVerbal;
use App\DenunciaMPMaie;
// use App\Imputado;
// use App\RelacionesImputado;
// use App\RelacionesImputadoDenunciantes;
// use App\RelacionesImputadoOfendidos;
use App\Workflow;
use Validator;
use App\Passport;
use App\Funcionario;

class DenunciaMPTools
{

  private $mp_required;
  private $log;
  private $workflow_type;

  public function __construct()
  {
      $this->log = new  \Log;
      $this->workflow_type = "mp";
      $this->mp_required =  Array("nacionalidad", "tipo_documento_identidad", "numero_documento_identidad","nombres", "primer_apellido", "segundo_apellido", "fecha_nacimiento", "sexo", "genero", "categoria");
  }

  // private function get_user_from_token($token){
  //     $user_details = $this->get_user($arr["token"]);
  //     $user = $this->get_institucion_dependencia($user_details->funcionario_id);
  //     $user->lugar = $this->get_lugar($user_details->funcionario_id);
  //     return $user;
  // }

  // public function get_user($token) {
  //     $user_details = "";
  //     $passport = new Passport;
  //     $user_details = $passport->details($token);
      
  //     if (empty($user_details)) {return "";}
  //     if (!property_exists($user_details, "code")) {return "";}
  //     if ($user_details->code != 200) {return "";}
  //     if (!property_exists($user_details, "contents")) {return "";}
  //     $contents = json_decode($user_details->contents);
  //     if (!property_exists($contents, "success")) {return "";} 

  //     return $contents->success;
  // }
  
  // public function get_institucion_dependencia($id) {
  //     $result = new \stdClass;
  //     try {
  //         $funcionario = Funcionario::findOrFail($id);
  //     } catch (\Exception $e) {
  //         $this->log::error($e);
  //         return $result;
  //     }
  //     $result->dependencia_id = $funcionario->dependencia_id;
  //     $result->institucion_id = $funcionario->dependencia()->first()->institucion_id;
  //     // call here get_lugar(Funcionario $funcionario)
  //     // $result->departamento_id = $res->departamento_id;
  //     // ...
  //     return $result;
  // }

  // private function get_lugar($funcionario_id) {
  //     $res = new \stdClass;
  //     $res->regional_id = null;
  //     $res->departamento_id = null;
  //     $res->municipio_id = null;
  //     $res->ciudad_ss_id= null;
  //     $res->aldea_ss_id = null;
  //     $res->colonia_ss_id = null;
  //     $res->sector_ss_id = null;
  //     $res->zona_ss_id = null;

  //     $result = json_decode(json_encode($res),true);

  //     $funcionario = Funcionario::whereId($funcionario_id)->with('dependencia.lugar')->first();

  //     if (is_null($funcionario)) {
  //         return $result;
  //     }

  //     if (is_null($funcionario->dependencia)) {
  //         return $result;
  //     }

  //     if (is_null($funcionario->dependencia->lugar)) {
  //         return $result;
  //     }

  //     if (is_null($funcionario->dependencia->lugar->institucionable)) {
  //         return $result;
  //     }

  //     $lugar = $funcionario->dependencia->lugar->institucionable;
  //     $res->regional_id = $lugar->regional_id;
  //     $res->departamento_id = $lugar->departamento_id;
  //     $res->municipio_id = $lugar->municipio_id;
  //     $res->ciudad_ss_id= $lugar->ciudad_ss_id;
  //     $res->aldea_ss_id = $lugar->aldea_ss_id;
  //     $res->colonia_ss_id = $lugar->colonia_ss_id;
  //     $res->sector_ss_id = $lugar->sector_ss_id;
  //     $res->zona_ss_id = $lugar->zona_ss_id;

  //     $result = json_decode(json_encode($res),true);
  //     return $result;
  // }

  private function general($denuncias, $denuncia_type) {
    $arr = [] ;
    foreach($denuncias as $d) {
      try {
        $dmp =  $d->tipo()->first();
        
        if (is_null($dmp)) { break; }
        $this->log::alert('up to $dmp');  
        $denuncia = $dmp->institucion()->first();

        if (is_null($denuncia)) { break; }
        $this->log::alert('up to $denuncia');        
        $doc = $denuncia->documento()->first();
        
        if (is_null($doc)) { break; }
        $this->log::alert('up to $doc');
        $lugar = $doc->lugares()->first();

        if (is_null($lugar)) { break; }
        $this->log::alert('up to $lugar');       
        $lugar_mp = $lugar->institucionable()->first();

        if (is_null($lugar_mp)) { break; }
        $this->log::alert('up to $lugar_ p');          
        $exp = $doc->expediente()->first();

        if (is_null($exp)) { break; }
        $this->log::alert('up to $exp');  
        $exp_mp = $exp->institucionable()->first();

        if (is_null($exp_mp)) { break; }
        $this->log::alert('up to $exp_mp');  

        $condition = (    isset($dmp)
                      and isset($denuncia)
                      and isset($doc)
                      and isset($lugar)
                      and isset($lugar_mp)
                      and isset($exp)
                      and isset($exp_mp)
                    );
        if (!$condition) { break;}
        $mp_denuncia = new \stdClass;
        $mp_denuncia->denuncia_id = $denuncia->id;
        $mp_denuncia->tipo = $denuncia_type;
        $mp_denuncia->required = $this->mp_required;
        $mp_denuncia->general = new \stdClass;
        $mp_denuncia->general->fecha_denuncia = date('Y/m/d',strtotime($denuncia->fecha_denuncia));
        $mp_denuncia->general->hora_denuncia = date('h:m:s',strtotime($denuncia->fecha_denuncia));;
        $mp_denuncia->general->departamento_id = $lugar_mp->departamento_id;
        $mp_denuncia->general->municipio_id = $lugar_mp->municipio_id;
        $mp_denuncia->general->aldea_id = $lugar_mp->aldea_id;
        $mp_denuncia->general->barrio_id = $lugar_mp->barrio_id;
        $mp_denuncia->general->caserio_id = $lugar_mp->caserio_id;
        $mp_denuncia->general->recepcionada_en = $dmp->recepcionada_en;
        $mp_denuncia->general->numero_expediente_rii = $exp->numero_expediente;
        $mp_denuncia->general->numero_expediente_mp = $exp_mp->numero_expediente;
        $mp_denuncia->general->numero_expediente_policial = $exp_mp->numero_expediente_policial;
        $mp_denuncia->general->numero_expediente_judicial = $exp_mp->numero_expediente_judicial;
        $mp_denuncia->general->numero_expediente_sedi = $exp_mp->numero_expediente_sedi;
        $mp_denuncia->general->numero_denuncia = $dmp->numero_denuncia;
        $arr[] = $mp_denuncia;
      } catch (\Exception $e) {
        $this->log::alert($e);
      }
    }
    return $arr;
  }

  private function hechos($d) {
    $hechos_arr = [] ;
    $denuncia = Denuncia::find($d) ;
    if (is_null($denuncia)) {return $hechos_arr; }
    $hechos = $denuncia->hechos()->get();
    if (is_null($hechos)) {return $hechos_arr;}
    foreach($hechos as $h) {
      $lugar = $h->lugar()->first();
      if (is_null($lugar)) {break;}
      $lugar_mp = null;
      try {
        $lugar_mp = $lugar->institucionable()->first();
      } catch (\Exception $e) {
        $this->log::alert('$lugar_id is '.$lugar->id);
        $this->log::alert($e);
      }
      if (is_null($lugar_mp)) {break;}
      $hecho = new \stdClass;
      $hecho->encabezado = new \stdClass;
      $hecho->encabezado->fecha_ocurrencia = date('Y/m/d',strtotime($h->fecha_ocurrencia));
      $hecho->encabezado->hora_ocurrencia = date('h:m:s',strtotime($h->fecha_ocurrencia));
      $hecho->encabezado->recepcionada_en = "";
      $hecho->encabezado->clase_lugar_hechos = $h->clase_lugar_hechos;
      $hecho->encabezado->departamento_id = $lugar_mp->departamento_id;
      $hecho->encabezado->municipio_id = $lugar_mp->municipio_id;
      $hecho->encabezado->aldea_id = $lugar_mp->aldea_id;
      $hecho->encabezado->barrio_id = $lugar_mp->barrio_id;
      $hecho->encabezado->caserio_id = $lugar_mp->caserio_id;
      $hecho->encabezado->direccion_detallada = $h->direccion_detallada;
      $hecho->narracion = $h->narracion;
      $hechos_arr[] = $hecho ;
    }
    return $hechos_arr;
  }

  private function get_persona_natural($id) {
    $persona_natural = PersonaNatural::find($id);
    return $persona_natural;
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

  private function denunciantes($d) {
    $denunciantes_arr = [] ;
    $denuncia = Denuncia::find($d) ;
    if (is_null($denuncia)) {return $denunciantes_arr; }
    $denunciantes = $denuncia->denunciantes()->get();
    if (is_null($denunciantes)) {return $denunciantes_arr;}
    foreach($denunciantes as $d) {
      $denunciante = new \stdClass;
      $denunciante->denunciante_id = $d->id;
      $persona_natural_id = $d->rol()->first()->persona_natural_id;
      $persona = $this->get_persona_natural($persona_natural_id);
      // $nombre = explode(' ', $nombres)[0];
      // $apellido1 = explode(' ',$nombres)[1];
      $denunciante->nombre = null;   
      $denunciante->apellido1 = null;
      $denunciante->nombres = null;         
      $denunciante->primer_apellido = null;
      $denunciante->segundo_apellido = null;
      $denunciante->genero = null;
      $denunciante->sexo = null;
      $denunciante->tipo_identidad = "portador";
      if (isset($persona)) {
        $denunciante->nombre = $persona->nombres;
        $denunciante->apellido1 = $persona->primer_apellido.', '.$persona->segundo_apellido;
        $denunciante->nombres = $persona->nombres;        
        $denunciante->primer_apellido = $persona->primer_apellido.', '.$persona->segundo_apellido;  
        $denunciante->segundo_apellido = $persona->segundo_apellido;
        $denunciante->genero = $persona->genero;
        $denunciante->sexo = $persona->sexo;    
        $denunciante->tipo_identidad = $this->tipo_identidad($persona);
      }
      $denunciante->persona_natural_id = $persona_natural_id;     
      $denunciantes_arr[] = $denunciante;
    }
    return $denunciantes_arr;
  }

  private function imputados($d) {
    $imputados_arr = [] ;
    $denuncia = Denuncia::find($d) ;
    if (is_null($denuncia)) {return $imputados_arr; }
    $imputados = $denuncia->imputados()->get();
    if (is_null($imputados)) {return $denunciantes_arr;}
    foreach($imputados as $i) {
      $imputado = new \stdClass;
      $imputado->denunciado_id = $i->id;
      $persona_natural_id = $i->rol()->first()->persona_natural_id;  
      $persona = $this->get_persona_natural($persona_natural_id);
      $imputado->nombre = "";
      $imputado->apellido1 = "";
      $imputado->nombres = null;         
      $imputado->primer_apellido = null;
      $imputado->segundo_apellido = null;
      $imputado->genero = null;
      $imputado->sexo = null;      
      $imputado->tipo_identidad = "portador";
      // $nombre = explode(' ', $nombres)[0];
      // $apellido1 = explode(' ',$nombres)[1];    
      if (isset($persona)) {
        $imputado->nombre = $persona->nombres;
        $imputado->apellido1 =$persona->primer_apellido;  
        $imputado->nombres = $persona->nombres;        
        $imputado->primer_apellido = $persona->primer_apellido.', '.$persona->segundo_apellido;  
        $imputado->segundo_apellido = $persona->segundo_apellido;
        $imputado->genero = $persona->genero;
        $imputado->sexo = $persona->sexo;     
        $imputado->tipo_identidad = $this->tipo_identidad($persona);
      }  
      $imputado->persona_natural_id = $persona_natural_id;  
      $imputado->armas_objetos_transportes = new \stdClass;
      $imputado->armas_objetos_transportes->armas = $i->armas;
      $imputado->armas_objetos_transportes->objetos = $i->objetos;
      $imputado->armas_objetos_transportes->transportes = $i->transportes;
      $imputado->movil_y_condicion = new \stdClass;
      $imputado->movil_y_condicion->moviles = $i->moviles;
      $imputado->movil_y_condicion->condiciones = $i->condiciones;
      $imputados_arr[] = $imputado;
    }
    return $imputados_arr;
  }

  private function ofendidos($d) {
    $ofendidos_arr = [] ;
    $denuncia = Denuncia::find($d) ;
    if (is_null($denuncia)) {return $ofendidos_arr; }
    $ofendidos = $denuncia->imputados()->get();
    if (is_null($ofendidos)) {return $ofendidos_arr;}
    foreach($ofendidos as $o) {
      $ofendido = new \stdClass;
      $ofendido->ofendido_id = $o->id;
      $persona_natural_id = $o->rol()->first()->persona_natural_id;   
      $persona = $this->get_persona_natural($persona_natural_id);
      $ofendido->nombre = "";
      $ofendido->apellido1 = "";   
      $ofendido->nombres = null;         
      $ofendido->primer_apellido = null;
      $ofendido->segundo_apellido = null;
      $ofendido->genero = null;
      $ofendido->sexo = null;      
      $ofendido->tipo_identidad = "portador";   
      if (isset($persona)) {
          $ofendido->nombre = $persona->nombres;
          $ofendido->apellido1 = $persona->primer_apellido.', '.$persona->segundo_apellido;            
          $ofendido->nombres = $persona->nombres;        
          $ofendido->primer_apellido = $persona->primer_apellido.', '.$persona->segundo_apellido;  
          $ofendido->segundo_apellido = $persona->segundo_apellido;
          $ofendido->genero = $persona->genero;
          $ofendido->sexo = $persona->sexo;     
          $ofendido->tipo_identidad = $this->tipo_identidad($persona);
      }
      $ofendido->persona_natural_id = $persona_natural_id;  
      $ofendido->delitos_sexuales = new \stdClass;
      $ofendido->delitos_sexuales->victima_embarazada = "";
      $ofendido->delitos_sexuales->frecuencia = "";
      $ofendido->delitos_sexuales->trabajo_remunerado = "";
      $ofendido->delitos_sexuales->asiste_centro_vocacional = "";
      $ofendido->delitos_sexuales->cantidad_hijos = "";
      $ofendido->suicidios = new \stdClass;
      $ofendido->suicidios->intentos_previos = "";
      $ofendido->suicidios->antecedentes_enfermedad_mental = "";
      $ofendido->suicidios->mecanismo_de_muerte = "";
      if (!(is_null($o->tipoable_type) and is_null($o->tipoable_id))) {
        if(strpos( $o->tipoable_type , 'Suicidio' ) !== false) {
          $suicidio = null;
          try {
            $suicidio = $id->tipoable()->first();
          } catch (\Exception $e ) { $this->log::alert($e); }
          if (!is_null($suicidio)) {
            $ofendido->suicidios->intentos_previos = $suicidio->intentos_previos;
            $ofendido->suicidios->antecedentes_enfermedad_mental = $suicidio->antecedentes_enfermedad_mental;
            $ofendido->suicidios->mecanismo_de_muerte = $suicidio->mecanismo;
          }
        }
        if(strpos( $o->tipoable_type , 'Sexual' ) !== false) {
          $delito_sexual = null;
          try {
            $delito_sexual = $id->tipoable()->first();
          } catch (\Exception $e ) { $this->log::alert($e); }
          if (!is_null($delito_sexual)) {
            $ofendido->delitos_sexuales->victima_embarazada = $delito_sexuale->victima_embarazada;
            $ofendido->delitos_sexuales->frecuencia = $delito_sexual->frecuencia;
            $ofendido->delitos_sexuales->trabajo_remunerado = $delito_sexual->trabajo_remunerado;
            $ofendido->delitos_sexuales->asiste_centro_vocacional = $delito_sexual->asiste_centro_vocacional;
            $ofendido->delitos_sexuales->cantidad_hijos = $delito_sexual->cantidad_hijos;
          }
        }
      }
      $ofendidos_arr[] = $ofendido;
    }
    return $ofendidos_arr;
  }

  private function relaciones($d) {
    $relaciones_arr = [] ;
    $imputados = Imputado::where('denuncia_id',$d)->get();
    if (is_null($imputados)) {return $relaciones_arr; }
    foreach($imputados as $r) {
      $relaciones = RelacionesImputado::where('imputado_id', $r->id)->get();
      if (is_null($relaciones)) {break;}
      foreach($relaciones as $rel) {
        $relacion = new \stdClass;
        $relacion->imputado_id = $r->id;
        $relacion->relacion_denunciantes = new \stdClass;
        $relacion->relacion_denunciantes->denunciante_id = "";
        $relacion->relacion_denunciantes->parentesco_id = "";
        $relacion->relacion_denunciantes->persona_natural_id = null;
        $relacion->relacion_denunciantes->primer_apellido = null;
        $relacion->relacion_denunciantes->segundo_apellido = null;
        $relacion->relacion_denunciantes->nombres = null;
        $relacion->relacion_denunciantes->genero = null;
        $relacion->relacion_denunciantes->sexo = null;
        $relacion->relacion_denunciantes->tipo_identidad = "portador";        
        $relacion->relacion_ofendidos = new \stdClass;
        $relacion->relacion_ofendidos->ofendido_id = "";
        $relacion->relacion_ofendidos->parentesco_id = "";
        $relacion->relacion_ofendidos->persona_natural_id = null;
        $relacion->relacion_ofendidos->primer_apellido = null;
        $relacion->relacion_ofendidos->segundo_apellido = null;
        $relacion->relacion_ofendidos->nombres = null;
        $relacion->relacion_ofendidos->genero = null;
        $relacion->relacion_ofendidos->sexo = null;
        $relacion->relacion_ofendidos->tipo_identidad = "portador";     
            
        if ( !(is_null($rel->relacionable_type) and is_null($rel->relacionable_id)) ) {
          // $this->log::alert('relacionable_type', $rel->relacionable_type);

          if(strpos( $rel->relacionable_type , 'Denunciante' ) !== false) {
            $denunciante = null;
            try {
              $relacionable = $rel->relacionable()->first();
              $denunciante = Denunciante::find($relacionable->denunciante_id);
            } catch (\Exception $e ) { $this->log::alert($e); }
            if (!is_null($denunciante)) {

              $this->log::alert('$denunciante...');
              $this->log::alert(json_encode($denunciante));

              $persona_natural_id = $denunciante->rol()->first()->persona_natural_id;  
              $persona = $this->get_persona_natural($persona_natural_id);

              if (isset($persona)) {
                $relacion->relacion_denunciantes->denunciante_id = $denunciante->denunciante_id;
                $relacion->relacion_denunciantes->parentesco_id = $rel->parentesco_id;
                $relacion->relacion_denunciantes->persona_natural_id = $persona_natural_id;
                $relacion->relacion_denunciantes->primer_apellido = $persona->primer_apellido;
                $relacion->relacion_denunciantes->segundo_apellido = $persona->segundo_apellido;
                $relacion->relacion_denunciantes->nombres = $persona->nombres;
                $relacion->relacion_denunciantes->genero = $persona->genero;
                $relacion->relacion_denunciantes->sexo = $persona->sexo;
                $relacion->relacion_denunciantes->tipo_identidad = $this->tipo_identidad($persona);
              }

            }
          }

          if(strpos( $rel->relacionable_type , 'Ofendido' ) !== false) {
            $ofendido = null;
            try {
              $relacionable = $rel->relacionable()->first();
              $ofendido = Ofendido::find($relacionable->denunciante_id);
            } catch (\Exception $e ) { $this->log::alert($e); }
            if (!is_null($ofendido)) {
              $relacion->relacion_ofendidos->ofendido_id = $ofendido->ofendido_id;
              $relacion->relacion_ofendidos->parentesco_id = $rel->parentesco_id;

              $persona_natural_id = $ofendido->rol()->first()->persona_natural_id;  
              $persona = $this->get_persona_natural($persona_natural_id);

              if (isset($persona)) {
                $relacion->relacion_ofendidos->denunciante_id = $denunciante->denunciante_id;
                $relacion->relacion_ofendidos->parentesco_id = $rel->parentesco_id;
                $relacion->relacion_ofendidos->persona_natural_id = $persona_natural_id;
                $relacion->relacion_ofendidos->primer_apellido = $persona->primer_apellido;
                $relacion->relacion_ofendidos->segundo_apellido = $persona->segundo_apellido;
                $relacion->relacion_ofendidos->nombres = $persona->nombres;
                $relacion->relacion_ofendidos->genero = $persona->genero;
                $relacion->relacion_ofendidos->sexo = $persona->sexo;
                $relacion->relacion_ofendidos->tipo_identidad = $this->tipo_identidad($persona);
              }

            }
          }
        }
        $relaciones_arr[] = $relacion;
      }
    }
    return $relaciones_arr;
  }

  private function workflow_actions($denuncia, $user_email) {
    $actions_arr = [];
    $denuncia_mp =$denuncia->tipo()->first();
    if (is_null($denuncia_mp)) {return $actions_arr; }

    $wf = new Workflow;
    $params = new \stdClass;
    $params->subject_id = $denuncia_mp->id;
    $params->object_id = $denuncia_mp->id;
    $params->workflow_type = "denuncia_m_p";  //$this->workflow_type;
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

  private function anexos($d) {
    $anexos_arr = [] ;
    $denuncia = Denuncia::find($d) ;
    if (is_null($denuncia)) {return $anexos_arr; }
    $anexos = $denuncia->anexos()->get();
    if (is_null($anexos)) {return $anexos_arr;}
    foreach($anexos as $a) {
      $anexo = new \stdClass;
      $anexo->id = $a->id;
      $anexo->documento_id = null;
      if (!empty($a->documento()->get())) {
        $anexo->documento_id = $a->documento()->first()->id;
      }
      $anexos_arr[] = $anexo;
    }
    return $anexos_arr;
  }

  private function get_denuncia($denuncia_tipo, $d_type, $token) {
    $denuncias_arr = [];

    $user_email = $this->get_email_from_token($token);
    if (!isset($user_email)) { return $denuncias_arr; }
    if (empty($user_email)) {return $denuncias_arr; }

    $denuncias = array($denuncia_tipo);
    $arr = $this->general($denuncias, $d_type);

    $this->log::alert(json_encode($arr));
    $this->log::alert('count of $arr is '. count($arr));
    foreach($arr as $denuncia) {
      $denuncia->hechos = new \stdClass;
      $denuncia->hechos = $this->hechos($denuncia->denuncia_id);
      $denuncia->denunciantes = $this->denunciantes($denuncia->denuncia_id);
      $denuncia->denunciados = $this->imputados($denuncia->denuncia_id);
      $denuncia->ofendidos = $this->ofendidos($denuncia->denuncia_id);
      $denuncia->relaciones = $this->relaciones($denuncia->denuncia_id);
      $denuncia->anexos = $this->anexos($denuncia->denuncia_id);
      $denuncia->actions = $this->workflow_actions($denuncia_tipo, $user_email); // watch-out: this line needs refactoring should the parent function be used in other services, note the $denuncia_tipo parameter, if is valid for future uses of this function
      $denuncias_arr[] = $denuncia;
    }
    return $denuncias_arr;
  }

  public function mp_denuncia($denuncia_id, $token){
    $res = new \stdClass;
    $denuncia = DenunciaMP::find($denuncia_id);
    if (is_null($denuncia)) { return json_encode($res); }
    $id = $denuncia->tipoable_id;

    $this->log::alert(json_encode($denuncia));

    if (preg_match('/DenunciaMPMaie/',$denuncia->tipoable_type)) {
      $denuncia_tipo = DenunciaMPMaie::find($id);
      $denuncia_type = "denuncia_maie";
    }
    if (preg_match('/DenunciaMPEscrita/',$denuncia->tipoable_type)) {
      $denuncia_tipo = DenunciaMPEscrita::find($id);
      $denuncia_type = "denuncia_escrita";
    }
    if (preg_match('/DenunciaMPVerbal/',$denuncia->tipoable_type)) {
      $denuncia_tipo = DenunciaMPVerbal::find($id);
      $denuncia_type = "denuncia_verbal";
    }
    if (!isset($denuncia_tipo)) { return json_encode($res); }

    $this->log::alert('[params] denuncia_tipo '.json_encode($denuncia_tipo));
    $this->log::alert('[params] denuncia_type '.$denuncia_type);

    $result = $this->get_denuncia($denuncia_tipo, $denuncia_type, $token);

    if (!isset($result)) { return json_encode($res); }
    if (empty($result)) { return json_encode($res); }

    $res->mp_denuncia = $result[0];
    $res->mp_denuncia->id = $denuncia_id;
    $res->mp_denuncia->tipoDenuncia = $denuncia_type;
    $json_result = json_encode($res);
    return $json_result;
  }

  private function headers(){ 
    $res = new \stdClass;
    $hdr = new \stdClass;
    $hdr->name = "tipo_documento";
    $hdr->label = "Tipo Documento";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;    
    $hdr->name = "numero_expediente";
    $hdr->label = "No. Expediente";
    $res->headers[] = $hdr;    
    $hdr = new \stdClass;    
    $hdr->name = "dependencia";
    $hdr->label = "Dependencia";
    $res->headers[] = $hdr;     
    $hdr = new \stdClass;    
    $hdr->name = "fecha_documento";
    $hdr->label = "Fecha Doc.";
    $res->headers[] = $hdr;   
    $hdr = new \stdClass;    
    $hdr->name = "workflow_state";
    $hdr->label = "Estado";
    $res->headers[] = $hdr;    
    // $hdr = new \stdClass;    
    // $hdr->name = "funcionario_asignado";
    // $hdr->label = "Asignado a";
    // $res->headers[] = $hdr;   
    $hdr = new \stdClass;    
    $hdr->name = "actions";
    $hdr->label = "Acciones";
    $res->headers[] = $hdr;                   
    return $res->headers;
  }
  
  private function tipo_documento($tipo_denuncia) {
    $denuncia_type = "";
    if (preg_match('/DenunciaMPMaie/',$tipo_denuncia)) {
      $denuncia_type = "Denuncia MAIE";
    }
    if (preg_match('/DenunciaMPEscrita/',$tipo_denuncia)) {
      $denuncia_type = "Denuncia Escrita";
    }
    if (preg_match('/DenunciaMPVerbal/',$tipo_denuncia)) {
      $denuncia_type = "Denuncia Verbal";
    }    
    return $denuncia_type;
  }

  private function titulo_documento($denuncia_mp) {
    $titulo_documento = "";

    if (is_null($denuncia_mp->institucion()->first() )) {
      return $titulo_documento;
    }

    if (is_null($denuncia_mp->institucion()->first()->documento()->first() )) {
      return $titulo_documento;
    }

    $titulo_documento = $denuncia_mp->institucion()->first()->documento()->first()->titulo_documento;

    if (is_null($titulo_documento)) {
      return "";
    }

    return $titulo_documento;
  }

  private function numero_expediente($denuncia_mp){
    $numero_expediente = "";
    if (is_null($denuncia_mp->institucion()->first() )) {
      return $numero_expediente;
    }
    if (is_null($denuncia_mp->institucion()->first()->documento()->first() )) {
      return $numero_expediente;
    }

    if (is_null($denuncia_mp->institucion()->first()->documento()->first()->expediente->first() )) {
      return $numero_expediente;
    }

    $doc = $denuncia_mp->institucion()->first()->documento()->first()->expediente()->first();

    $numero_expediente = $doc->numero_expediente;
    return $numero_expediente;    
  }

  private function dependencia($denuncia_mp){
    $dependencia = "";
    if (is_null($denuncia_mp->institucion())) 
      { return $dependencia;}
    if (is_null($denuncia_mp->institucion()->first()->documento())) 
      { return $dependencia;}
    if (is_null($denuncia_mp->institucion()->first()->documento()->first())) 
      { return $dependencia;}    
    if (
        is_null($denuncia_mp->institucion()->first()->documento()->first()->expediente() )
       ) 
      { return $dependencia;}
    if (
        is_null($denuncia_mp->institucion()->first()->documento()->first()->expediente()->first() )
      ) 
      { return $dependencia;}
    if (
        is_null($denuncia_mp->institucion()->first()->documento()->first()->expediente()->first()->dependencia() )
      ) 
      { return $dependencia;}

    $dependencia = $denuncia_mp->institucion()->first()->documento()->first()->expediente()->first()->dependencia()->first()->nombre;

    if (is_null($dependencia)) {
      return "";
    }
    return $dependencia;  
  }

  private function acciones($token, $denuncia_mp) {
    $acciones = [];

    $user_email = $this->get_email_from_token($token);
    if (!isset($user_email)) { 
      $this->log::alert('user_email is null');
      return $acciones; 
    }
    
    if (empty($user_email)) {
      $this->log::alert('user_email is empty');
      return $acciones; 
    }

    $id = $denuncia_mp->tipoable_id;

    if (preg_match('/DenunciaMPMaie/',$denuncia_mp->tipoable_type)) {
      $denuncia_tipo = DenunciaMPMaie::find($id);
    }
    if (preg_match('/DenunciaMPEscrita/',$denuncia_mp->tipoable_type)) {
      $denuncia_tipo = DenunciaMPEscrita::find($id);
    }
    if (preg_match('/DenunciaMPVerbal/',$denuncia_mp->tipoable_type)) {
      $denuncia_tipo = DenunciaMPVerbal::find($id);
    }    
    $acciones = $this->workflow_actions($denuncia_tipo, $user_email);    
    $acciones[] = 'Expediente';

    $this->log::alert('acciones are ...');
    $this->log::alert(json_encode($acciones));
    
    return $acciones;
  }

  private function rows($token) {
    $res = new \stdClass;
    //$res->rows[]=[]; this fails is no data is returned...
    foreach (DenunciaMP::All() as $dmp) {
      $row = new \stdClass;
      $row->documento_id = $dmp->id;
      $row->actions = $this->acciones($token, $dmp);
      //$row->actions = $this->workflow_actions($denuncia_tipo, $user_email); 
      $row->updated_at = date('Y/m/d',strtotime($dmp->updated_at));
      $row->dependencia = $this->dependencia($dmp);
      $row->numero_expediente = $this->numero_expediente($dmp);
      $row->tipo_documento = $this->tipo_documento($dmp->tipoable_type);
      $row->titulo_documento = $this->titulo_documento($dmp);
      $row->workflow_state = $dmp->workflow_state;
      $res->rows[] = $row;
    }
    return $res->rows;
  }

  public function mp_list_denuncias($token) {
    $res = new \stdClass;
    $res->headers = $this->headers();
    $res->rows = $this->rows($token);
    return $res ;
  }
}

