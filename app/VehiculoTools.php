<?php

namespace App;

use App\Workflow;
use Validator;
use App\Passport;
use App\Dependencia;
use App\Denunciante;
use App\Vehiculo;
use App\FuncionarioSS;
use App\Lugar;
use App\LugarSS;
use App\PersonaNatural;
use App\Rol;
use App\Fiscal;
use App\DenunciaSS;

class VehiculoTools
{

  private $vehiculo_required;
  private $log;
  private $workflow_type;

  public function __construct()
  {
      $this->log = new  \Log;
      $this->workflow_type = "vehiculo_registrado";
  }

  private function get_persona_natural($id) {
    $persona_natural = PersonaNatural::find($id);
    return $persona_natural;
  }

  //cambion find vehiculo este get vehiculo es otro endpoint
  private function get_vehiculo($arr) {
    $placa = $arr["placa"];
    $vin = $arr["vin"];
    $motor = $arr["motor"];
    $chasis = $arr["chasis"];
    $placaid = Vehiculo::where('placa',$placa);
    $vinid = Vehiculo::where('vin',$vin);
    $motorid = Vehiculo::where('motor',$motor);
    $chasisid = Vehiculo::where('chasis',$chasis);
    //$sujeto = $sujeto::where('denuncia_id',$denuncia_id);
    //$funcionario = Funcionario::whereId($funcionario_id)->with('dependencia.lugar')->first();
    //$vehiculo = Vehiculo::find($arr);
    if (!is_null($placaid)) {return $placaid;}
    if (!is_null($vinid)) {return $vinid;}
    if (!is_null($motorid)) {return $motorid;}
    if (!is_null($chasisid)) {return $chasisid;}

  }

  private function propietario($id) {
    $propietario_arr;
    $vehiculo = Vehiculo::find($id);
    $propietario = new \stdClass;
    $persona_natural_id = $vehiculo->id_propietario;
    if (is_null($persona_natural_id)) {return $propietario_arr;}

     $persona = $this->get_persona_natural($persona_natural_id);
     if (is_null($persona)) {return $propietario_arr;}

     $propietario->identidad = $persona->id;
     $propietario->nombres = $persona->nombres;
     $propietario->apellidos = $persona->primer_apellido.', '.$persona->segundo_apellido;
     $propietario->licencia = $vehiculo->licencia;

      $propietario_arr = $propietario;
      return $propietario_arr;
  }

  private function lugar_encuentro($id) {
      $lugarss;
      $vehiculo = Vehiculo::find($id);
      $lugar_encuentro = new \stdClass;
      $idlugar = $vehiculo->id_lugar;
      $lugar_ss = LugarSS::find($idlugar);
      //$s = Lugar::institucionable($lugar_ss);
        if (is_null($lugar_ss)) {return $lugarss;}
      //if (is_null($lugar_ss)) {return $lugarss_arr;}

        $lugar_encuentro->regional_id = $lugar_ss->regional_id;
        $lugar_encuentro->departamento_id = $lugar_ss->departamento_id;
        $lugar_encuentro->municipio_id = $lugar_ss->municipio_id;
        $lugar_encuentro->aldea_id = $lugar_ss->aldea_ss_id;
        $lugar_encuentro->colonia_id = $lugar_ss->colonia_ss_id;
        $lugar_encuentro->ciudad_id = $lugar_ss->ciudad_ss_id;
        $lugar_encuentro->sector_id = $lugar_ss->sector_ss_id;

        $lugarss_arr = $lugar_encuentro;
        return $lugarss_arr;
  }

  private function funcionario($id) {
      $funcionarios_arr = [];
      $vehiculo = Vehiculo::find($id);
      $responsable = new \stdClass;
      $funcionarioss = $vehiculo->id_funcionario;

      //funcionarios
      $funcionarios_ss = FuncionarioSS::find($funcionarioss);
      if (is_null($funcionarios_ss)) {return $funcionarios_arr;}
        $funcionario = $funcionarios_ss->institucion()->first();
        $funcionarioid = $funcionario->id;
        $funcionario_policia = Funcionario::find($funcionarioid);
        $rol_funcionario = $funcionario_policia->rol()->first();
        $persona_natural_id = $rol_funcionario->persona_natural_id;
        $persona = $this->get_persona_natural(50);

        //funcionario
        $responsable->nombres = $persona->nombres;
        $responsable->apellidos = $persona->primer_apellido.', '.$persona->segundo_apellido;
        $responsable->placa = $funcionarios_ss->placa;
        $funcionarios_arr[] = $responsable;
        return $funcionarios_arr;
  }

  private function encontrado_por($id) {
      $funcionarios_arr = [];
      $vehiculo = Vehiculo::find($id);
      $responsable = new \stdClass;
      $funcionarioss = $vehiculo->id_responsable;

      //funcionarios
      $funcionarios_ss = FuncionarioSS::find($funcionarioss);
      if (is_null($funcionarios_ss)) {return $funcionarios_arr;}
        $funcionario = $funcionarios_ss->institucion()->first();
        $funcionarioid = $funcionario->id;
        $funcionario_policia = Funcionario::find($funcionarioid);
        $rol_funcionario = $funcionario_policia->rol()->first();
        $persona_natural_id = $rol_funcionario->persona_natural_id;
        $persona = $this->get_persona_natural(50);

        //funcionario
        $responsable->nombres = $persona->nombres;
        $responsable->apellidos = $persona->primer_apellido.', '.$persona->segundo_apellido;
        $responsable->placa = $funcionarios_ss->placa;
        $funcionarios_arr[] = $responsable;
        return $funcionarios_arr;
  }

  private function lugar_custodia($id) {

    $unidad_arr;
    $vehiculo = Vehiculo::find($id);
    $lugar_custodia = new \stdClass;
    $idlugar = $vehiculo->id_unidad;

      if (is_null($idlugar)) {return null;}

      $unidad = Dependencia::find($idlugar);
      $lugar_custodia->id_unidad = $unidad->institucion_id;
      $lugar_custodia->nombre = $unidad->nombre;

        $unidad_arr = $lugar_custodia;
        return $unidad_arr;
  }

  private function vehiculo($id) {
    $vehiculo_arr;
    $vehiculo = Vehiculo::find($id);
    $vehiculos = new \stdClass;

    $vehiculos->numero_vehiculo = $vehiculo->id;
    $vehiculos->tipo = $vehiculo->tipo;
    $vehiculos->marca = $vehiculo->marca;
    $vehiculos->modelo = $vehiculo->modelo;
    $vehiculos->placa = $vehiculo->placa;
    $vehiculos->año = $vehiculo->año;
    $vehiculos->color = $vehiculo->color;
    $vehiculos->motor = $vehiculo->motor;
    $vehiculos->chasis = $vehiculo->chasis;
    $vehiculos->vin = $vehiculo->vin;
    $vehiculos->descripcion = $vehiculo->descripcion;
    $vehiculos->estado = $vehiculo->estado;

    $vehiculos->fecha_registro = date('Y/m/d',strtotime($vehiculo->fecha_registro));
    $vehiculos->fecha_encuentro = date('Y/m/d',strtotime($vehiculo->fecha_encuentro));

    $vehiculos->id_denuncia = $vehiculo->id_denuncia;
    $vehiculos->id_orden_captura = $vehiculo->id_orden_captura;

      $vehiculo_arr = $vehiculos;
      return $vehiculo_arr;
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

  private function get_vehiculos($vehiculo, $id, $token) {
    $vehiculo_arr = [];

    $vehiculos = new \stdClass;

    $vehiculos->propietario = $this->propietario($id);
    $vehiculos->lugar_retencion = $this->lugar_encuentro($id);
    $vehiculos->lugar_retencion = $this->lugar_custodia($id);
    $vehiculos->vehiculo = $this->vehiculo($id);
    $vehiculos->funcionario = $this->funcionario($id);
    $vehiculos->funcionario = $this->encontrado_por($id);
    $vehiculo_arr[] = $vehiculos;
    return $vehiculo_arr;
  }

  public function ss_vehiculo($vehiculo_id, $token){
    $res = new \stdClass;
    $vehiculo = Vehiculo::find($vehiculo_id);

    if (is_null($vehiculo)) { return json_encode($res); }
    $id = $vehiculo->id;
    $result = $this->get_vehiculos($vehiculo, $id, $token);

    if (!isset($result)) { return json_encode($res); }
    if (empty($result)) { return json_encode($res); }

    $json_result = json_encode($result);
    return $json_result;

  }

  private function headers(){
    $res = new \stdClass;
    $hdr = new \stdClass;
    $hdr->name = "placa";
    $hdr->label = "Placa Vehiculo";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "tipo";
    $hdr->label = "Tipo Vehiculo";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "marca";
    $hdr->label = "Marca Vehiculo";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "modelo";
    $hdr->label = "Modelo Vehiculo";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "estado";
    $hdr->label = "Estado";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "propietario";
    $hdr->label = "Propietario";
    $res->headers[] = $hdr;
    return $res->headers;
  }

  private function vehiculopropietario($vehiculo){
    $idpersona = $vehiculo->id_propietario;
    $temp = PersonaNatural::find($idpersona);
    $persona_natural_id = $temp->id;
    $persona = $this->get_persona_natural($persona_natural_id);
    $nombre = "";

    if (is_null($persona)) {return $nombre;}
     $nombre = $persona->nombres.', '.$persona->primer_apellido;
     return $nombre;
  }

  private function detalle($vehiculo){
    $detalle = "";
    if (!is_null($vehiculo->id_denuncia)) {
      $detalle = "Denuncia";
      return $detalle;
    }

    if (!is_null($vehiculo->id_allanamiento)) {
      $detalle = "Allanamiento";
      return $detalle;
    }

    if (!is_null($vehiculo->id_orden_captura)) {
      $detalle = "Dictamen";
      return $detalle;
    }

    if (!is_null($vehiculo->id_decomiso)) {
      $detalle = "Decomiso";
      return $detalle;
    }

    if (!is_null($vehiculo->id_expediente)) {
      $detalle = "Expediente";
      return $detalle;
    }

  }

  private function rows($token) {
    $res = new \stdClass;
    $res->rows = [];
    //$res->rows[]=[]; this fails is no data is returned...
    foreach (Vehiculo::All() as $dmp) {
      $row = new \stdClass;
      $row->numero_vehiculo = $dmp->id;
      $row->placa = $dmp->placa;
      $row->tipo = $dmp->tipo;
      $row->marca = $dmp->marca;
      $row->modelo = $dmp->modelo;
      $row->fecha_registro = date('Y/m/d',strtotime($dmp->fecha_registro));
      $row->estado = $dmp->estado;
      $row->propietario = $this->vehiculopropietario($dmp);
      $row->updated_at = date('Y/m/d',strtotime($dmp->updated_at));
      $res->rows[] = $row;
    }
    return $res->rows;
  }

  public function ss_list_vehiculo($token) {
    $res = new \stdClass;
    $res->headers = $this->headers();
    $res->rows = $this->rows($token);
    return $res ;
  }
}
