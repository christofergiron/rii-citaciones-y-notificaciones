<?php

namespace App;

use App\OrdenCaptura;
use App\OrdenCapturaPersona;
use App\OrdenCapturaPersonaNatural;
use App\OrdeCapturaMenor;
use App\OrdenCapturaPersonaMenor;
use App\OrdenCapturaVehiculo;
use App\OrdenCapturaXVehiculo;
use App\OrdenCapturaEstado;
use App\Juez;
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

class BuscarOrdenCapturaTools
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

  private function imputado($id) {
    $persona_natural;
    $sospechoso = new \stdClass;
    $persona = $this->get_persona_natural($id);

    $sospechoso->nombres = $persona->nombres;
    $sospechoso->apellidos = $persona->primer_apellido.', '.$persona->segundo_apellido;
    $persona_natural = $sospechoso;
    return $persona_natural;
  }

  private function imputado_menor($id) {
    $persona_natural;
    $sospechoso = new \stdClass;
    $menor = Personamenor::find($id);
    $id_menor = $menor->persona_natural_id;

    $persona = $this->get_persona_natural($id_menor);

    $sospechoso->nombres = $persona->nombres;
    $sospechoso->apellidos = $persona->primer_apellido.', '.$persona->segundo_apellido;
    $persona_natural = $sospechoso;
    return $persona_natural;
  }

  private function vehiculo($id) {
    $vehiculos_arr;
    $vehiculos = new \stdClass;

    $vehiculo = $this->get_vehiculo($id_vehiculo);
    $vehiculos->tipo = $vehiculo->tipo;
    $vehiculos->marca = $vehiculo->marca;
    $vehiculos->modelo = $vehiculo->modelo;
    $vehiculos->placa = $vehiculo->placa;
    $vehiculos->año = $vehiculo->año;
    $vehiculos->color = $vehiculo->color;
    $vehiculos->motor = $vehiculo->motor;
    $vehiculos->chasis = $vehiculo->chasis;
    $vehiculos->vin = $vehiculo->vin;

    $vehiculos_arr = $vehiculos;
    return $vehiculos_arr;
  }

  private function orden_captura_persona($id, $persona_id) {
    $ordenes_captura_arr;
    $orden_captura = OrdenCaptura::find($id);
    $ordenes_capturas = new \stdClass;

    $ordenes_capturas->numero_orden_captura = $orden_captura->id;
    $ordenes_capturas->estado = $orden_captura->estado;
    $ordenes_capturas->fecha_creacion = date('Y/m/d',strtotime($orden_captura->fecha_creacion));
    $ordenes_capturas->id_expediente = $orden_captura->id_expediente;
    //$ordenes_capturas->delito = $this->delitos($id);
    //$ordenes_capturas->imputado = $this->imputado($persona_id);
    $ordenes_captura_arr = $ordenes_capturas;
    return $ordenes_capturas;
  }

  private function orden_captura_menor($id, $persona_menor_id) {
    $ordenes_captura_arr;
    $orden_captura = OrdenCaptura::find($id);
    $ordenes_capturas = new \stdClass;

    $ordenes_capturas->numero_orden_captura = $orden_captura->id;
    $ordenes_capturas->estado = $orden_captura->estado;
    $ordenes_capturas->fecha_creacion = date('Y/m/d',strtotime($orden_captura->fecha_creacion));
    $ordenes_capturas->id_expediente = $orden_captura->id_expediente;
    $ordenes_capturas->delito = $this->delitos($id);
    $ordenes_capturas->imputado = $this->imputado_menor($persona_menor_id);
    return $ordenes_captura_arr;
  }

  private function orden_captura_vehiculo($id, $id_vehiculo) {
    $ordenes_captura_arr;
    $orden_captura = OrdenCaptura::find($id);
    $ordenes_capturas = new \stdClass;

    $ordenes_capturas->numero_orden_captura = $orden_captura->id;
    $ordenes_capturas->estado = $orden_captura->estado;
    $ordenes_capturas->fecha_creacion = date('Y/m/d',strtotime($orden_captura->fecha_creacion));
    $ordenes_capturas->id_expediente = $orden_captura->id_expediente;
    $ordenes_capturas->delito = $this->delitos($id);
    $ordenes_capturas->imputado = $this->vehiculo($id_vehiculo);
    return $ordenes_captura_arr;
  }

  public function buscar_orden_captura_persona($persona_id, $token){
    $idor = 0;
    $res = new \stdClass;
    //solo cambiar esto por el id_persona natural y listo
    $persona = PersonaNatural::where('id_persona_natural',$persona_id);
    if (is_null($persona)) {
      $verificar = "verifique la identidad de la persona";
      return $verificar;
    }
    $id = $persona->id;
    $orden_persona_natural = OrdenCapturaPersonaNatural::where('id_persona',$id)->get();
    if (is_null($orden_persona_natural)) {
       return null;
    }
    foreach($orden_persona_natural as $op) {
      $orden_persona_temp = $op->id_orden_captura;
      if (is_null($orden_persona_temp)) {
        $error = "la orden de captura fue alterada";
        return $error;
      }
      $orden_persona = OrdenCapturaPersona::find($orden_persona_temp);
      if (is_null($orden_persona_temp)) {
        $error = "la orden de captura fue alterada";
        return $error;
      }
      $orden = $orden_persona->orden()->first();
      if (is_null($orden)) {
        $error = "la orden de captura fue alterada";
        return $error;
      }

      $orden_estado = $orden->estado;
      if (is_null($orden_estado)) {
        $error = "la orden de captura fue alterada";
        return $error;
      }

      if ($orden_estado == "Vigente") {
        $idor = $orden->id;
        return $idor;
        break;
      }
    }
     return "sin ordenes de captura";
  }

  public function buscar_orden_captura_menor($persona_id, $token){
    $idor = 0;
    $res = new \stdClass;
    $persona = PersonaNatural::find($persona_id);
    if (is_null($persona)) {
      $verificar = "verifique la identidad de la persona";
      return $verificar;
    }
    $id = $persona->id;
    $orden_persona_natural = OrdenCapturaPersonaMenor::where('id_persona_menor',$id)->get();
    if (is_null($orden_persona_natural)) {
       return null;
    }
    foreach($orden_persona_natural as $op) {
      $orden_persona_temp = $op->id_orden_captura;
      if (is_null($orden_persona_temp)) {
        $error = "la orden de captura fue alterada";
        return $error;
      }
      $orden_persona = OrdenCapturaMenor::find($orden_persona_temp);
      if (is_null($orden_persona_temp)) {
        $error = "la orden de captura fue alterada";
        return $error;
      }
      $orden = $orden_persona->orden()->first();
      if (is_null($orden)) {
        $error = "la orden de captura fue alterada";
        return $error;
      }

      $orden_estado = $orden->estado;
      if (is_null($orden_estado)) {
        $error = "la orden de captura fue alterada";
        return $error;
      }

      if ($orden_estado == "Vigente") {
        $idor = $orden->id;
        return $idor;
        break;
      }
    }
     return "sin ordenes de captura";
  }

  public function buscar_orden_captura_vehiculo($placa, $token){
    $idor = 0;
    $res = new \stdClass;
    $vehiculo = Vehiculo::where('placa', $placa)->first();
    if (is_null($vehiculo)) {
      $verificar = "verifique la placa del vehiculo";
      return $verificar;
    }
    $id_vehiculo = $vehiculo->placa;
    $id = $vehiculo->id;
    $orden_x_vehiculo = OrdenCapturaXVehiculo::where('id_vehiculo',$id)->get();
    if (is_null($orden_x_vehiculo)) {
       return null;
    }
    foreach($orden_x_vehiculo as $op) {
      $orden_vehiculo_temp = $op->id_orden_captura;
      if (is_null($orden_vehiculo_temp)) {
        $error = "la orden de captura fue alterada";
        return $error;
        //break;
      }
      $orden_vehiculo = OrdenCapturaVehiculo::find($orden_vehiculo_temp);

      if (is_null($orden_vehiculo_temp)) {
        $error = "la orden de captura fue alterada";
        return $error;
        //break;
      }
      $orden = $orden_vehiculo->orden()->first();
      if (is_null($orden)) {
        $error = "la orden de captura fue alterada";
        return $error;
        //break;
      }
      $orden_estado = $orden->estado;
      if (is_null($orden_estado)) {
        $error = "la orden de captura fue alterada";
        return $error;
        //break;
      }
      if ($orden_estado == "Vigente") {
        $idor = $orden->id;
        return $idor;
        break;
      }

    }
     return "sin ordenes de captura";
  }

}
