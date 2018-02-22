<?php

namespace App;

use Psr\Log\LoggerInterface;
// use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
// use App\User;
// use Illuminate\Support\Facades\Auth;
use Validator;

class Tools
{

  public function __construct(LoggerInterface $logger)
  {
      $this->logger = $logger;
  }

  public function validate_params($arr) {
    $validator = Validator::make($arr, [
        "name" => "required",
        "email" => "required|email",
        "funcionario_id" => "required",
        "password" => "required",
        "c_password" => "required|same:password",
    ]);
    return $validator;
  }

  public function parse_request($request){
    $_request = $request->all();
    $this->logger->alert($_request);
    $arr = json_decode($_request[0], true );

    $this->logger->alert($arr);

    $input = json_decode($_request[0]) ;//$arr //$request->all();
    if (property_exists($input, 'password')){
      $input->password = bcrypt($input->password);
    }
    $input_encoded = json_encode($input);
    $arr_input = json_decode($input_encoded, true);
    // $this->logger->alert($arr);
    $result = array();
    $result[] = $arr_input;
    $result[] = $arr;
    return $result;
  }

}
