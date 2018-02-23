<?php

namespace App\Http\Controllers\API;

use App\Tools;
use App\DictamenVehicular;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Requests\StoreDictamenVehicular;

class DictamenVehicularController extends Controller
{

    public  $successStatus = 200;
    private $root;
    private $dictamen_vehicular_tools;
    private $StoreDictamenVehicular;

    public function __construct(LoggerInterface $logger)
    {
        $this->log = new \Log;
        $this->logger = $logger;
        $this->tools = new Tools($this->logger);
        $this->$dictamen_vehicular_tools = new DictamenVehicularTools;
        $this->root = false;
        $this->StoreDictamenVehicular = new StoreDictamenVehicular;
    }

    public function index(Request $request){
      # parsing
      // $parsed_request = $this->tools->parse_request($request);
      // $arr = $parsed_request[1];
      $arr = $request->all();
      // $id = $request->params->id

       $validator = Validator::make($arr   , [
         "token" => "required",
       ]);

       if ($validator->fails()) {
         return response()->json(['error'=>'No Content due to null or empty parameters'], 403);
       }

       #get dictamen_vehicular
       $res = $this->$dictamen_vehicular_tools->ss_list_dictamenesvehiculares($arr["token"]);

       # chek for nulls
       if (is_null($res)) {
         return response()->json(['error'=>'No Content due to null or empty parameters'], 403);
       }

       # return success response
       return response()->json(['documentsTable'=>$res], $this->successStatus);
    }

    public function show(Request $request, $id) {
      # parsing
      // $parsed_request = $this->tools->parse_request($request);
      // $arr = $parsed_request[1];
      $arr = $request->all();
      $this->log::alert($arr);


       $validator = Validator::make($arr, [
         "token" => "required"
       ]);

       if ($validator->fails()) {
         return response()->json(['error'=>'No Content due to null or empty parameters'], 403);
       }

       #get dictamen_vehicular
       $res = $this->$dictamen_vehicular_tools->dictamen_vehicular($id, $arr["token"]);

       # chek for nulls
       if (is_null($res)) {
         return response()->json(['error'=>'No Content due victamen vehicular is invalid!'], 403);
       }

       # return success response
       return response()->json(['success' => true, 'message'=>json_decode($res)], $this->successStatus);
    }

    public function store(Request $request) { //StoreDictamenVehicular $request
      # parsing
      // $parsed_request = $this->tools->parse_request($request);
      // $arr = $parsed_request[1];
      $arr = $request->all();
      $this->logger->alert('inside Store DictamenVehicular');
      $this->logger->alert(json_encode($arr)) ;
      $res = $this->StoreDictamenVehicular->rules($arr);

      if ($res->code != 200) {
        return response()->json(['error'=>$res->message], 403);
      }

      $res = $this->StoreDictamenVehicular->persist($arr);

      if (!$res->success) {
        return response()->json(['error'=>$res->message], 403);
      }
       # return success response
       return response()->json(['success' => $res->success, 'message'=>$res->message, 'payload' => $res->payload], $this->successStatus);
    }

    public function workflow(Request $request) {
      $arr = $request->all();
      $this->logger->alert('inside workflow service DictamenVehicular');
      $this->logger->alert(json_encode($arr)) ;
      $res = $this->StoreDictamenVehicular->workflow_rules($arr);

      if ($res->code != 200) {
        return response()->json(['error'=>$res->message], 403);
      }

      //$res = $this->StoreDictamenVehicular->asignar_delitos($arr);
      $res = $this->StoreDictamenVehicular->apply_transition($arr);

      if ($res->code != 200) {
        return response()->json(['error'=>$res->message], 403);
      }

       # return success response
       return response()->json(['success' => true, 'message'=>$res], $this->successStatus);
    }

}
