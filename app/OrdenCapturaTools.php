<?php

namespace App;
use App\OrdenCaptura;
use App\OrdenCapturaDelito;
use App\OrdenCapturaEstado;
use App\OrdeCapturaMenor;
use App\OrdenCapturaPersona;
use App\OrdenCapturaPersonaMenor;
use App\OrdenCapturaPersonaNatural;
use App\OrdenCapturaVehiculo;
use App\OrdenCapturaXVehiculo;
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

class OrdenCapturaTools
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

  private function get_vehiculo($id) {
    $vehiculo = Vehiculo::find($id);
    if (is_null($vehiculo)) {return null;}
    return $vehiculo;
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

  private function tipo_orden_captura($id) {
      $tipo_orden_captura_arr;
      $orden_captura = OrdenCaptura::find($id);
      if (is_null($orden_captura)) { return null; }

      $tipo_orden_captura = new \stdClass;

      $id_ordenable = $orden_captura->ordenable_id;

      $this->log::alert(json_encode($orden_captura));
      if (preg_match('/OrdenCapturaPersona/',$orden_captura->ordenable_type))
      {
        $orden_persona = OrdenCapturaPersona::find($id_ordenable);
        $id_orden = $orden_persona->id;

        $tipo_orden_captura->orden_captura_persona = 1;
        $tipo_orden_captura->orden_captura_menor = 0;
        $tipo_orden_captura->orden_captura_vehiculo = 0;
        $tipo_orden_captura->id = $id_orden;
        $tipo_orden_captura->razon = $orden_persona->razon;
        $tipo_orden_captura->observaciones = $orden_persona->observaciones;
        $tipo_orden_captura->imputado = $this->captura_persona_natural($id_orden);
        $tipo_orden_captura_arr = $tipo_orden_captura;
        return $tipo_orden_captura_arr;

      }
      if (preg_match('/OrdenCapturaMenor/',$orden_captura->ordenable_type))
      {
        $orden_menor = OrdenCapturaMenor::find($id_ordenable);
        $id_orden = $orden_menor->id;

        $tipo_orden_captura->orden_captura_persona = 0;
        $tipo_orden_captura->orden_captura_menor = 1;
        $tipo_orden_captura->orden_captura_vehiculo = 0;
        $tipo_orden_captura->id = $id_orden;
        $tipo_orden_captura->razon = $orden_menor->razon;
        $tipo_orden_captura->observaciones = $orden_menor->observaciones;
        $tipo_orden_captura->menor_infractor = $this->captura_persona_menor($id_orden);
        $tipo_orden_captura_arr = $tipo_orden_captura;
        return $tipo_orden_captura_arr;
      }
      if (preg_match('/OrdenCapturaVehiculo/',$orden_captura->ordenable_type))
      {
        $orden_menor = OrdenCapturaVehiculo::find($id_ordenable);
        $id_orden = $orden_menor->id;

        $tipo_orden_captura->orden_captura_persona = 0;
        $tipo_orden_captura->orden_captura_menor = 0;
        $tipo_orden_captura->orden_captura_vehiculo = 1;
        $tipo_orden_captura->id = $id_orden;
        $tipo_orden_captura->razon = $orden_menor->razon;
        $tipo_orden_captura->observaciones = $orden_menor->observaciones;
        $tipo_orden_captura->vehiculo = $this->captura_vehiculo($id_orden);
        $tipo_orden_captura_arr = $tipo_orden_captura;
        return $tipo_orden_captura_arr;
      }
  }

  private function captura_persona_natural($id) {
      $detalle_arr;
      $detalle = new \stdClass;
      if (is_null($id)) {
        $detalle_arr = $detalle;
        return $detalle_arr;
      }

      $orden_persona = OrdenCapturaPersonaNatural::where('id_orden_captura',$id)->first();
      //$temp = $orden_persona->id_persona;
      //echo $orden_persona;
      $persona = $this->get_persona_natural($orden_persona->id_persona);
      if (is_null($persona)) {
        $detalle->identidad = null;
        $detalle->nombres = null;
        $detalle->apellidos = null;
        $detalle->direccion = $orden_persona->direccion;
        $detalle->motivo = $orden_persona->motivo;

        $detalle_arr = $detalle;
        return $detalle_arr;
      }
      $detalle->identidad = $persona->id;
      $detalle->nombres = $persona->nombres;
      $detalle->apellidos = $persona->primer_apellido.', '.$persona->segundo_apellido;
      $detalle->direccion = $orden_persona->direccion;
      $detalle->motivo = $orden_persona->motivo;

      $detalle_arr = $detalle;
      return $detalle_arr;
  }

  private function captura_persona_menor($id) {
      $detalle_arr;
      $detalle = new \stdClass;
      if (is_null($id)) {
        $detalle_arr = $detalle;
        return $detalle_arr;
      }
      $orden_menor = OrdenCapturaPersonaMenor::where('id_orden_captura',$id)->first();

      $id_persona_menor = $orden_menor->id_persona_menor;
      $persona_menor = PersonaMenor::find($id_persona_menor);
      $id_persona_natural = $persona_menor->persona_natural_id;

      $persona = $this->get_persona_natural($id_persona_natural);
      if (is_null($persona)) {
        $detalle->identidad = null;
        $detalle->nombres = null;
        $detalle->apellidos = null;
        $detalle->direccion = $orden_menor->direccion;
        $detalle->motivo = $orden_menor->motivo;

        $detalle_arr = $detalle;
        return $detalle_arr;
      }
      $detalle->identidad = $persona->id;
      $detalle->nombres = $persona->nombres;
      $detalle->apellidos = $persona->primer_apellido.', '.$persona->segundo_apellido;
      $detalle->direccion = $orden_menor->direccion;
      $detalle->motivo = $orden_menor->motivo;

      $detalle_arr = $detalle;
      return $detalle_arr;
  }

  private function captura_vehiculo($id) {
      $detalle_arr;
      $detalle = new \stdClass;
      if (is_null($id)) {
        $detalle_arr = $detalle;
        return $detalle_arr;
      }
      $orden_vehiculo = OrdenCapturaXVehiculo::where('id_orden_captura',$id)->first();

      $id_vehiculo = $orden_vehiculo->id_vehiculo;

      $vehiculo = $this->get_vehiculo($id_vehiculo);
      if (is_null($vehiculo)) {
        $detalle->tipo = null;
        $detalle->marca = null;
        $detalle->modelo = null;
        $detalle->placa = null;
        $detalle->año = null;
        $detalle->color = null;
        $detalle->motor = null;
        $detalle->chasis = null;
        $detalle->vin = null;
        $detalle->motivo = $orden_vehiculo->motivo;

        $detalle_arr = $detalle;
        return $detalle_arr;
      }
      $detalle->tipo = $vehiculo->tipo;
      $detalle->marca = $vehiculo->marca;
      $detalle->modelo = $vehiculo->modelo;
      $detalle->placa = $vehiculo->placa;
      $detalle->año = $vehiculo->año;
      $detalle->color = $vehiculo->color;
      $detalle->motor = $vehiculo->motor;
      $detalle->chasis = $vehiculo->chasis;
      $detalle->vin = $vehiculo->vin;
      $detalle->motivo = $orden_vehiculo->motivo;

      $detalle_arr = $detalle;
      return $detalle_arr;
  }

  private function delitos($id) {
      $delitos_arr = [];
      $orden_captura = OrdenCaptura::find($id);
      //$id_orden = $orden_captura->id;
      $delitos = $orden_captura->delitos()->get();
      if (is_null($delitos)) {return $delitos_arr;}
      foreach($delitos as $d) {
        $delito = new \stdClass;

        $delito->id_orden_captura = $d->id_orden_captura;
        $delito->tipo_delito = $d->tipo_delito;
        $delito->delito = $d->delito;
        $delito->id_victima = $d->id_victima;
        $delito->perjudicado = $d->perjudicado;
        $delito->descripcion = $d->descripcion;

        $delitos_arr[] = $delito;
        unset($delito);
      }
      return $delitos_arr;
  }

  private function cambio_estado($id) {
      $estados_arr = [];
      $orden_captura = OrdenCaptura::find($id);

      $estados = $orden_captura->estados()->get();

      if (is_null($estados)) {
        $tempo = $orden_captura->estado;
        return $tempo;
      }
      foreach($estados as $d) {
        $estado = new \stdClass;

         if (is_null($d->estado_antiguo)) {
           $estado->estado_antiguo = $orden_captura->estado;
         }

        $estado->estado_antiguo = $d->estado_antiguo;
        $estado->fecha = date('Y/m/d',strtotime($d->fecha));
        $estado->estado_nuevo = $d->estado_nuevo;
        $estado->fecha_cambio = date('Y/m/d',strtotime($d->updated_at));
        $estado->motivo = $d->motivo;

        if (!is_null($d->id_contra_orden)) {
           $estado->id_contra_orden = $d->id_contra_orden;
        }
        $estado->funcionario = $d->id_funcionario;

        $estados_arr[] = $estado;
        unset($estado);
      }
      return $estados_arr;
  }

  private function orden_captura($id) {
    $ordenes_captura_arr;
    $orden_captura = OrdenCaptura::find($id);
    $ordenes_capturas = new \stdClass;

    $ordenes_capturas->numero_orden_captura = $orden_captura->id;
    $ordenes_capturas->estado = $orden_captura->estado;
    $ordenes_capturas->fecha_creacion = date('Y/m/d',strtotime($orden_captura->fecha_creacion));
    $ordenes_capturas->id_expediente = $orden_captura->id_expediente;
    $ordenes_capturas->descripcion = $orden_captura->descripcion;
    $ordenes_capturas->etapa = $orden_captura->id_etapa;
    $ordenes_capturas->audiencia = $orden_captura->id_audiencia;

    $ordenes_captura_arr = $ordenes_capturas;
    return $ordenes_captura_arr;
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

  private function get_orden_captura($id, $token) {
    $orden_captura_arr = [];

    $ordenes_captura = new \stdClass;

    $ordenes_captura->orden_captura = $this->orden_captura($id);
    $ordenes_captura->juez = $this->juez($id);
    //cambio
    //$ordenes_captura->etapa = $this->etapa($id);
    //$ordenes_captura->audiencia = $this->audiencia($id);
    //aqui van lo requerido (persona,menor,vehiculo)
    $ordenes_captura->tipo_orden_captura = $this->tipo_orden_captura($id);
    $ordenes_captura->delitos = $this->delitos($id);
    $temp = OrdenCaptura::find($id);
    //$id_orden = $orden_captura->id;
    $estados = $temp->estados()->get();
    if (!is_null($estados)) {
      $ordenes_captura->estados = $this->cambio_estado($id);
    }
    $orden_captura_arr = $ordenes_captura;
    return $orden_captura_arr;
  }

  public function pj_orden_captura($orden_captura_id, $token){
    $res = new \stdClass;
    $orden_captura = OrdenCaptura::find($orden_captura_id);
    if (is_null($orden_captura)) { return json_encode($res); }
    $id = $orden_captura->id;

    $result = $this->get_orden_captura($id, $token);

    if (!isset($result)) { return json_encode($res); }
    if (empty($result)) { return json_encode($res); }

    $json_result = json_encode($result);
    return $json_result;

  }

  private function headers(){
    $res = new \stdClass;
    $hdr = new \stdClass;
    $hdr->name = "id_orden_captura";
    $hdr->label = "Numero Orden Captura";
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
    $hdr->name = "tipo_orden";
    $hdr->label = "Tipo Orden Captura";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "estado";
    $hdr->label = "Estado";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "workflow_state";
    $hdr->label = "Estado";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "actions";
    $hdr->label = "Acciones";
    $res->headers[] = $hdr;
    return $res->headers;
  }

  private function tipoorden($orden_captura){
    $tipo_captura = "";

    if (preg_match('/Persona/',$orden_captura->ordenable_type))
    { $tipo_captura = "Persona";
      return $tipo_captura;
    }

    if (preg_match('/Menor/',$orden_captura->ordenable_type))
    { $tipo_captura = "niño";
      return $tipo_captura;
    }

    if (preg_match('/Vehiculo/',$orden_captura->ordenable_type))
    { $tipo_captura = "Vehiculo";
      return $tipo_captura;
    }

  }

  private function rows($token) {
    $res = new \stdClass;
    //$res->rows[]=[]; this fails is no data is returned...
    foreach (OrdenCaptura::All() as $dmp) {
      $row = new \stdClass;
      $row->id_orden_captura = $dmp->id;
      $row->id_expediente = $dmp->id_expediente;
      $row->fecha_creacion = date('Y/m/d',strtotime($dmp->fecha_creacion));
      $row->tipo_orden = $this->tipoorden($dmp);
      $row->estado = $dmp->estado;
      $row->updated_at = date('Y/m/d',strtotime($dmp->updated_at));
      $res->rows[] = $row;
    }
    return $res->rows;
  }

  public function pj_list_orden_captura($token) {
    $res = new \stdClass;
    $res->headers = $this->headers();
    $res->rows = $this->rows($token);
    return $res ;
  }
}
