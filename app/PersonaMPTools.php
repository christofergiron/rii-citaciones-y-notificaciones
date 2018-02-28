<?php

namespace App;

use App\PersonaNatural;
use App\PersonaNaturalMP;
use Validator;
use App\Passport;

class PersonaMPTools
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

  private function headers(){ 
    $res = new \stdClass;
    $hdr = new \stdClass;
    $hdr->name = "nombres";
    $hdr->label = "Nombre";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;    
    $hdr->name = "apellidos";
    $hdr->label = "Apellidos";
    $res->headers[] = $hdr;    
    $hdr = new \stdClass;    
    $hdr->name = "tipo_persona";
    $hdr->label = "Tipo";
    $res->headers[] = $hdr;     
    $hdr = new \stdClass;    
    $hdr->name = "sexo";
    $hdr->label = "Sexo";
    $res->headers[] = $hdr;   
    $hdr = new \stdClass;    
    $hdr->name = "nacionalidad";
    $hdr->label = "Nacionalidad";
    $res->headers[] = $hdr;    
    $hdr = new \stdClass;    
    $hdr->name = "estado_civil";
    $hdr->label = "Estado Civil";
    $res->headers[] = $hdr;   
    $hdr = new \stdClass;    
    $hdr->name = "fecha_nacimiento";
    $hdr->label = "Fecha Nacimiento";
    $res->headers[] = $hdr;                   
    return $res->headers;
  }

  private function rows() {
    $res = new \stdClass;
    $iteracion = PersonaNatural::where('institucionable_type','App\PersonaNaturalMP')->get();
    foreach ($iteracion as $persona) {
      $row = new \stdClass;
      $row->persona_natural_id = $persona->id;
      $row->nombres = $persona->nombres;
      $row->persona = $persona->nombres;
      $row->apellidos = $persona->primer_apellido.', '.$persona->segundo_apellido;
      $row->tipo_persona = "Natural";
      $row->sexo = $persona->sexo;
      $row->nacionalidad = $persona->nacionalidad;
      $row->estado_civil = $persona->estado_civil;
      $row->fecha_nacimiento = date('Y/m/d',strtotime($persona->fecha_nacimiento));

      $res->rows[] = $row;
    }
    return $res->rows;
  }

  public function mp_list_personas() {
    $res = new \stdClass;
    $res->headers = $this->headers();
    $res->rows = $this->rows();
    return $res ;
  }

  private function get_array($arr) {
    $res = [];
    if (!is_null($arr)) {
      $res = $arr;
    }
    return $res;
  }

  private function get_menor($persona) {
    $res = null;
    $menor = $persona->menor()->first();

    if (isset($menor)) {
      $res = $menor->representante_legal_id;
    }

    return $res;
  }

  private function get_abogado($persona_natural) {
    $res = null;
    $persona = $persona_natural->persona()->first();

    if (!isset($persona)) {
      return $res;
    }

    $abogado = $persona->abogado()->first();

    if (isset($abogado)) {
      $res = $abogado->identificacion_colegio_abogados;
    }

    return $res;
  }

  private function get_direccion_template() {
    $direccion = new \stdClass;
    $direccion->departamento_id = null;
    $direccion->municipip_id = null;
    $direccion->aldea_mp_id = null;
    $direccion->barrio_mp_id = null;
    $direccion->caserio_mp_id = null;   
  }

  private function get_direcciones($pmp) {
    $direcciones = $pmp->direcciones();

    if (!isset($direcciones)) {
      $res = [$this->get_direccion_template];      
    }
    $res = [];
    
    foreach($direcciones as $d) { 
      $direccion = new \stdClass;
      $direccion->departamento_id = $d->departamento_id;
      $direccion->municipip_id = $d->municipip_id;
      $direccion->aldea_mp_id = $d->aldea_mp_id;
      $direccion->barrio_mp_id = $d->barrio_mp_id;
      $direccion->caserio_mp_id = $d->caserio_mp_id;
      $res[] = $direccion;
    }

    return $res;
  }

  private function persona_natural_mp($persona) {
    $res = new \stdClass;
    $res->categorias = [];
    $res->tipo_identificacion = null;
    $res->alias = [];
    $res->direcciones = [];

    $pmp = $persona->institucionable()->first();

    if (isset($pmp)) {
      $res->categorias = $pmp->categorias;
      $res->tipo_identificacion = $pmp->tipo_identificacion;
      $res->alias = $pmp->alias;
      $res->direcciones = $this->get_direcciones($pmp);
    }

    return $res;
  }

  public function persona($arr) {
    $res = new \stdClass;
    $persona = PersonaNatural::find($arr["persona_natural_id"])->first();
    
    if (!isset($persona)) {
        return null;
    }

    $res->persona_natural = new \stdClass;
    $res->persona_natural->nombres = $persona->nombres;
    $res->persona_natural->primer_apellido = $persona->primer_apellido;
    $res->persona_natural->segundo_apellido = $persona->segundo_apellido;
    $res->persona_natural->genero = $persona->genero;
    $res->persona_natural->sexo = $persona->sexo;
    $res->persona_natural->nacionalidad = $persona->nacionalidad;
    $res->persona_natural->tipo_documento_identidad = $persona->tipo_documento_identidad;
    $res->persona_natural->fecha_nacimiento = $persona->fecha_nacimiento;
    $res->persona_natural->edad = $persona->edad;
    $res->persona_natural->estado_civil = $persona->estado_civil;
    $res->persona_natural->telefono_notificacion = $persona->telefono_notificacion;
    $res->persona_natural->correo_notificacion = $persona->correo_notificacion;
    $res->persona_natural->apartado_postal_notificacion = $persona->apartado_postal_notificacion;
    $res->persona_natural->ocupaciones = $this->get_array($persona->ocupaciones);
    $res->persona_natural->profesiones = $this->get_array($persona->profesiones);
    $res->persona_natural->etnias = $this->get_array($persona->etnias);    
    $res->persona_natural->escolaridad = $this->get_array($persona->escolaridad);
    $res->persona_natural->discapacidades = $this->get_array($persona->discapacidades);
    $res->persona_natural->pueblo_indigena = $this->get_array($persona->pueblo_indigena);    
    $res->persona_natural->telefonos = $this->get_array($persona->telefonos);  
    $res->persona_natural->correos_electronicos = $this->get_array($persona->correos_electronicos);
    $res->persona_natural->apartados_postales = $this->get_array($persona->apartados_postales);

    // persona menor
    $res->persona_menor = new \stdClass;
    $res->persona_menor->representante_legal_id = $this->get_menor($persona);

    // persona abogado
    $res->persona_abogado = new \stdClass;
    $res->persona_abogado->identificacion_colegio_abogados = $this->get_abogado($persona);

    // persona_natural_mp
    $pmp = $this->persona_natural_mp($persona);
    $res->persona_natural_mp = new \stdClass;
    $res->persona_natural_mp->categorias = $pmp->categorias;
    $res->tipo_identificacion = $pmp->tipo_identificacion;
    $res->alias = $pmp->alias;
    $res->direcciones = $pmp->direcciones;

    return $res;
  }
}

