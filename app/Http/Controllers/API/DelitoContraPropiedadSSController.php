<?php

namespace App\Http\Controllers\API;

use App\Tools;
use App\DelitoContraPropiedadSS;
use App\DelitoContraPropiedadSSTools;
use App\Http\Requests\StoreDelitoContraPropiedadSS;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;

class DelitoContraPropiedadSSController extends Controller
{

    public  $successStatus = 200;
    private $root;
    private $delito_contra_propiedad_ss_tools;
    private $StoreDelitoContraPropiedadSS;

    public function __construct(LoggerInterface $logger)
    {
        $this->log = new \Log;
        $this->logger = $logger;
        $this->tools = new Tools($this->logger);
        $this->delito_contra_propiedad_ss_tools = new DelitoContraPropiedadSSTools;
        $this->root = false;
        $this->StoreDelitoContraPropiedadSS = new StoreDelitoContraPropiedadSS;
    }

    public function index(Request $request){
      $arr = $request->all();

       $validator = Validator::make($arr   , [
         "token" => "required",
       ]);

       if ($validator->fails()) {
         return response()->json(['error'=>'No Content due to null or empty parameters'], 403);
       }

       #get denuncia_mp
       $res = $this->delito_contra_propiedad_ss_tools->ss_list_solicitudes($arr["token"]);

       # chek for nulls
       if (is_null($res)) {
         return response()->json(['error'=>'No Content due to null or empty parameters'], 403);
       }

       # return success response
       return response()->json(['solicitudTable'=>$res], $this->successStatus);
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

       #get denuncia_mp
       $res = $this->delito_contra_propiedad_ss_tools->get_solicitud($id, $arr["token"]);

       # chek for nulls
       if (is_null($res)) {
         return response()->json(['error'=>'No Content due denuncia mp is invalid!'], 403);
       }

       # return success response
       return response()->json(['success' => true, 'message'=>json_decode($res)], $this->successStatus);
    }

    public function store(Request $request) {
      $arr = $request->all();

      $this->logger->alert('inside Store DenunciaMP');
      $this->log::alert(json_encode($arr));

      $res = $this->StoreDelitoContraPropiedadSS->rules($arr);

      if ($res->code != 200) {
        return response()->json(['error'=>$res->message], 403);
      }

      $res = $this->StoreDelitoContraPropiedadSS->persist($arr);

      if (!$res->success) {
        return response()->json(['error'=>$res->message], 403);
      }

       # return success response
       return response()->json(['success' => $res->success, 'message'=>$res->message, 'payload' => $res->payload], $this->successStatus);
    }

    public function workflow(Request $request) {
      $arr = $request->all();
      $this->logger->alert('inside workflow service DenunciaMP');
      $this->logger->alert(json_encode($arr)) ;

      $res = $this->StoreDelitoContraPropiedadSS->workflow_rules($arr);

      if ($res->code != 200) {
        return response()->json(['error'=>$res->message], 403);
      }

      //$res = $this->StoreDenunciaMP->asignar_delitos($arr);
      $res = $this->StoreDelitoContraPropiedadSS->apply_transition($arr);

      if ($res->code != 200) {
        return response()->json(['error'=>$res->message], 403);
      }

       # return success response
       return response()->json(['success' => true, 'message'=>$res], $this->successStatus);
    }

}
