<?php
namespace App;
use Validator;
use App\Passport;
class InformeLogisticoSSTools
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
  private function acciones($token, $informe_logistico_ss) {
      $acciones = [];
      $user_email = $this->get_email_from_token($token);
      if (!isset($user_email)) { return $acciones; }
      if (empty($user_email)) {return $acciones; }
      $acciones = $this->workflow_actions($informe_logistico_ss, $user_email);
      $acciones[] = 'Informe';
      return $acciones;
  }

  private function headers(){
      $res = new \stdClass;
      $hdr = new \stdClass;
      $hdr->name = "id";
      $hdr->label = "ID";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "fecha";
      $hdr->label = "Fecha";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "titulo";
      $hdr->label = "Proceso Investigativo";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "numero_oficio";
      $hdr->label = "Numero de Oficio";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "institucion";
      $hdr->label = "Institucion";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "solicitado_por";
      $hdr->label = "Solicitado Por: ";
      $res->headers[] = $hdr;
      return $res->headers;
  }
  private function rows($token) {
    $res = new \stdClass;
    //$res->rows[]=[]; this fails is no data is returned...
    $iteracion = Informe::where('tipoable_type','App\InformeLogisticoSS')->get();

    foreach ($iteracion as $informe) {
      $row = new \stdClass;
      $row->id = $informe->id;
      $row->fecha = $informe->fecha;
      $row->titulo = $informe->titulo;
      $row->numero_oficio = $informe->numero_oficio;
      $row->institucion = $informe->institucion;
      $row->solicitado_por = $informe->solicitado_por;
      //$row->actions = $this->acciones($token, $solicitud);
      //$row->updated_at = date('Y/m/d',strtotime($solicitud->updated_at));
      //$row->workflow_state = $solicitud->solicitable()->get();
      $res->rows[] = $row;
    }
    return $res->rows;
  }
  private function obtener_informe($informe, $token) {
      $informe_arr = [];
      $user_email = $this->get_email_from_token($token);
      if(empty($user_email)){
        $user_email = "ssworker@gmail.com";
      }
      $this->log::alert("email: ".$user_email);

      if (!isset($user_email)) { return $informe_arr; }
      if (empty($user_email)) {return $informe_arr; }
      $item = new \stdClass;
      // $item->updated_at = date('Y/m/d',strtotime($solicitud_record_historial->updated_at));
      // $item->dependencia = 'Secretaría de Seguridad';
      if (isset($exp)) {
         $this->log::info("valor: ".print_r($exp));
      }else{
        $this->log::info("viene nulo");
      }
      //$item->titulo_documento = $this->titulo_documento($juez);
      $item->id = $informe->id;
      $item->fecha = $informe->fecha;
      $item->titulo = $informe->titulo;
      $item->numero_oficio = $informe->numero_oficio;
      $item->institucion = $informe->institucion;
      $item->solicitado_por = $informe->solicitado_por;
      $item->descripcion = $informe->descripcion;
      $item->informe_logistico_ss = $informe->tipoable()->get();
      $item->procesos_investigativos = $informe->hitos_informe_ss()->get();
      $item->armas_investigacion = $informe->armas()->get();
      $item->sospechosos_investigacion = $informe->sospechosos()->get();

      $item->sospechosos_denuncia = [];

      $denuncia = Denuncia::find($informe->id_denuncia);

      $sospechosos_denuncia = Sospechoso::
        where("denuncia_id", $denuncia->id)->get();

      foreach ($sospechosos_denuncia as $sospechoso_node) {
        $sospechoso = new \stdClass;

        $persona = $sospechoso_node->rol()->first()->persona()->first();

        $sospechoso->nombre = $persona->primer_apellido." ".$persona->nombres;
        $sospechoso->delitos = $this->getDelitos($sospechoso_node);
        $sospechoso->detenido = $sospechoso_node->detenido;
        $sospechoso->remitido = $sospechoso_node->remitido;

        $item->sospechosos_denuncia[] = $sospechoso;
      }

    $item->testigos_denuncia = [];

     $testigos = Testigo::
        where("denuncia_id", $denuncia->id)->get();

      foreach ($testigos as $testigo_node) {
        $testigo = new \stdClass;

        $persona = $testigo_node->rol()->first()->persona()->first();
        $testigo->persona_natural_id = $persona->id;
        $testigo->nombre = $persona->primer_apellido." ".$persona->nombres;
        $testigo->residente = $testigo_node->residente;
        $item->testigos_denuncia[] = $testigo;
      }

      $informe_arr[] = $item;
      return $informe_arr;
  }

  public function get_informe($informe_id, $token){
      $res = new \stdClass;

      $informe = Informe::find($informe_id);
      if (is_null($informe)) {
        return json_encode($res);
      }
      $id = $informe->id;
      $this->log::alert("objeto actual: ".json_encode($informe));

      $result = $this->obtener_informe($informe, $token);

      if (!isset($result)) { return json_encode($res); }
      if (empty($result)) { return json_encode($res); }

      $res->informe = $result[0];

      $json_result = json_encode($res);
      return $json_result;
  }


  private function getDelitos($sospechoso){
    $res = new \stdClass;

    $delitos_id = DelitoAtribuido::
      where("sospechoso_id", $sospechoso->id)
      ->select('delito_id')->get()->toArray();

    $delitos = Delito::whereIn('id', $delitos_id)->get();

    if(!isset($delitos) || count($delitos) < 1){
      return array();
    }

    foreach ($delitos as $delito) {
        $row = new \stdClass;

        $row->id = $delito->id;
          $row->nombre = $delito->descripcion;

        $res->rows[] = $row;
      }

    return $res->rows;
  }

  public function ss_list_informes($token) {
      $res = new \stdClass;
      $res->headers = $this->headers();
      $res->rows = $this->rows($token);
      return $res ;
  }
}
?>
