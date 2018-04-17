<?php
namespace App;
use Validator;
use App\Passport;
class SospechosoInvestigacionSSTools
{
  private $ss_require;
    private $log;
    public function __construct()
    {
        $this->log = new  \Log;
        //$this->$ss_require =  Array("");
        //$this->$ss_require =  Array("nacionalidad", "tipo_documento_identidad", "numero_documento_identidad","nombres", "primer_apellido", "segundo_apellido", "fecha_nacimiento", "sexo", "genero", "categoria");
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
  private function acciones($token, $sospechoso_investigacion_ss) {
      $acciones = [];
      $user_email = $this->get_email_from_token($token);
      if (!isset($user_email)) { return $acciones; }
      if (empty($user_email)) {return $acciones; }
      $acciones = $this->workflow_actions($sospechoso_investigacion_ss, $user_email);
      $acciones[] = 'Sospechoso de Investigacion';
      return $acciones;
  }

  private function headers(){
      $res = new \stdClass;
      $hdr = new \stdClass;
      $hdr->name = "id";
      $hdr->label = "ID";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "alias";
      $hdr->label = "Alias";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "otros_nombres";
      $hdr->label = "Otros Nombres";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "forma_cara";
      $hdr->label = "Forma de la Cara";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "contextura";
      $hdr->label = "Contextura";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "tono_voz";
      $hdr->label = "Tono de Voz: ";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "discapacidad";
      $hdr->label = "Discapacidad: ";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "peso";
      $hdr->label = "Peso: ";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "estatura";
      $hdr->label = "Estatura: ";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "tipo_sangre";
      $hdr->label = "Tipo de Sangre: ";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "zona";
      $hdr->label = "Zona Visto: ";
      $res->headers[] = $hdr;
      return $res->headers;
  }
  private function rows($token) {
    $res = new \stdClass;
    //$res->rows[]=[]; this fails is no data is returned...

      foreach (SospechosoInvestigacionSS::All() as $sospechoso) {
        $row = new \stdClass;
        $row->id = $sospechoso->id;
        $row->alias = $sospechoso->alias;
        $row->otros_nombres = $sospechoso->otros_nombres;
        $row->forma_cara = $sospechoso->forma_cara;
        $row->contextura = $sospechoso->contextura;
        $row->tono_voz = $sospechoso->tono_voz;
        $row->discapacidad = $sospechoso->discapacidad;
        $row->peso = $sospechoso->peso;
        $row->estatura = $sospechoso->estatura;
        $row->tipo_sangre = $sospechoso->tipo_sangre;
        $row->zona = $sospechoso->zona;
        //$row->actions = $this->acciones($token, $solicitud);
        //$row->updated_at = date('Y/m/d',strtotime($solicitud->updated_at));
        //$row->workflow_state = $solicitud->solicitable()->get();
        $res->rows[] = $row;
      }
      return $res->rows;
  }
  private function obtener_sospechosos($sospechoso, $token) {
      $sospechoso_arr = [];
      $user_email = $this->get_email_from_token($token);
      if(empty($user_email)){
        $user_email = "ssworker@gmail.com";
      }
      $this->log::alert("email: ".$user_email);

      if (!isset($user_email)) { return $sospechoso_arr; }
      if (empty($user_email)) {return $sospechoso_arr; }
      $item = new \stdClass;
      // $item->updated_at = date('Y/m/d',strtotime($solicitud_record_historial->updated_at));
      // $item->dependencia = 'Secretaría de Seguridad';
      if (isset($exp)) {
         $this->log::info("valor: ".print_r($exp));
      }else{
        $this->log::info("viene nulo");
      }
      //$item->titulo_documento = $this->titulo_documento($juez);
      $item->id = $sospechoso->id;
      $item->alias = $sospechoso->alias;
      $item->otros_nombres = $sospechoso->otros_nombres;
      $item->caracteristicas = $sospechoso->caracteristicas;
      $item->forma_cara = $sospechoso->forma_cara;
      $item->contextura = $sospechoso->contextura;
      $item->tono_voz = $sospechoso->tono_voz;
      $item->discapacidad = $sospechoso->discapacidad;
      $item->peso = $sospechoso->peso;
      $item->estatura = $sospechoso->estatura;
      $item->tipo_sangre = $sospechoso->tipo_sangre;
      $item->cicatrices = $sospechoso->cicatrices;
      $item->zona = $sospechoso->zona;
      $item->descripcion_zona = $sospechoso->descripcion_zona;
      $item->delitos = $sospechoso->delitos()->get();
      $sospechoso_arr[] = $item;
      return $sospechoso_arr;
  }

  public function get_sospechosos($sospechoso_id, $token){
      $res = new \stdClass;

      $sospechoso= SospechosoInvestigacionSS::find($sospechoso_id);
      if (is_null($sospechoso)) {
        return json_encode($res);
      }
      $id = $sospechoso->id;
      $this->log::alert("objeto actual: ".json_encode($sospechoso));

      $result = $this->obtener_sospechosos($sospechoso, $token);

      if (!isset($result)) { return json_encode($res); }
      if (empty($result)) { return json_encode($res); }

      $res->sospechoso = $result[0];

      $json_result = json_encode($res);
      return $json_result;
  }
  public function ss_list_sospechosos($token) {
      $res = new \stdClass;
      $res->headers = $this->headers();
      $res->rows = $this->rows($token);
      return $res ;
  }
}
?>
