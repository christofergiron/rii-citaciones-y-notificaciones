<?php

namespace App;

use Validator;
use App\NUE;

Class DefaultSSNUE implements iNUE {

    private $response;
    private $institucion_id;
    private $dependencia_id;
    private $expediente_id;
    private $initial_nue;
    private $default_expediente_id;

    public function __construct()
    {
        $this->institucion_id = 3; //SS
        $this->dependencia_id = null;
        $this->expediente_id = "SS-".$this->year_month_in_string().'-';
        $this->initial_nue = 30000;
        $this->default_expediente_id = 9999999999;
        $this->log = new \Log;
        $this->response = null;
    }

  private function year_month_in_string() {
    $now = new \DateTime('now');
    $year = $now->format('Y');
    $month = $now->format('m');
    return $year.'-'.$month;
  }

  public function generate() {

      // create next correlativo
      $last_nue = NUE::orderBy('correlativo','desc')->where('institucion_id',$this->institucion_id)->first();

      if (is_null($last_nue)) { 
        $last_nue = new \stdClass;
        $last_nue->correlativo = $this->initial_nue; 
      }

      $new_nue = $last_nue->correlativo + 1;
      $expediente_id = $this->expediente_id.strval($new_nue);

      $nue_arr = [
          "institucion_id" => $this->institucion_id,
          "dependencia_id" => $this->dependencia_id,
          "correlativo" => $new_nue,
          "expediente_id" => $expediente_id
      ];

      try {
        $nue_mp = NUE::create($nue_arr);
      } catch(\Exception $e) {
        $expediente_id = $this->expediente_id.$this->default_expediente_id;
        throw new \Exception('NUE could not be created!');
      }

      return $expediente_id;    
  }
  
}