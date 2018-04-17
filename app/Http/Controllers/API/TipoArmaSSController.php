<?php

namespace App\Http\Controllers\API;

use App\Tools;
//use App\ArmaSS;
use App\TipoArmaSSTools;
use App\Http\Requests\StoreArmaSS;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;

class TipoArmaSSController extends Controller
{

    public  $successStatus = 200;
    private $root;
    private $tipo_arma_tools;
    //private $StoreArmaSS;

    public function __construct(LoggerInterface $logger)
    {
        $this->log = new \Log;
        $this->logger = $logger;
        $this->tools = new Tools($this->logger);
        $this->tipo_arma_tools = new TipoArmaSSTools;
        $this->root = false;
      //  $this->StoreArmaSS = new StoreArmaSS;
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
       $res = $this->tipo_arma_tools->ss_list_tipo_arma($arr["token"]);

       # chek for nulls
       if (is_null($res)) {
         return response()->json(['error'=>'No Content due to null or empty parameters'], 403);
       }

       # return success response
       return response()->json(['TipoArmaTable'=>$res], $this->successStatus);
    }

    // public function show(Request $request, $id) {
    //   # parsing
    //   // $parsed_request = $this->tools->parse_request($request);
    //   // $arr = $parsed_request[1];
    //   $arr = $request->all();
    //   $this->log::alert($arr);
    //
    //
    //    $validator = Validator::make($arr, [
    //      "token" => "required"
    //    ]);
    //
    //    if ($validator->fails()) {
    //      return response()->json(['error'=>'No Content due to null or empty parameters'], 403);
    //    }
    //
    //    #get denuncia_mp
    //    $res = $this->armas_ss_tools->get_arma($id, $arr["token"]);
    //
    //    # chek for nulls
    //    if (is_null($res)) {
    //      return response()->json(['error'=>'No Content due denuncia mp is invalid!'], 403);
    //    }
    //
    //    # return success response
    //    return response()->json(['success' => true, 'message'=>json_decode($res)], $this->successStatus);
    // }
    //
    // public function store(Request $request) {
    //   $arr = $request->all();
    //
    //   $this->logger->alert('inside Store DenunciaMP');
    //   $this->log::alert(json_encode($arr));
    //
    //   $res = $this->StoreArmaSS->rules($arr);
    //
    //   if ($res->code != 200) {
    //     return response()->json(['error'=>$res->message], 403);
    //   }
    //
    //   $res = $this->StoreArmaSS->persist($arr);
    //
    //   if (!$res->success) {
    //     return response()->json(['error'=>$res->message], 403);
    //   }
    //
    //    # return success response
    //    return response()->json(['success' => $res->success, 'message'=>$res->message, 'payload' => $res->payload], $this->successStatus);
    // }
    //
    // public function workflow(Request $request) {
    //   $arr = $request->all();
    //   $this->logger->alert('inside workflow service DenunciaMP');
    //   $this->logger->alert(json_encode($arr)) ;
    //
    //   $res = $this->StoreArmaSS->workflow_rules($arr);
    //
    //   if ($res->code != 200) {
    //     return response()->json(['error'=>$res->message], 403);
    //   }
    //
    //   //$res = $this->StoreDenunciaMP->asignar_delitos($arr);
    //   $res = $this->StoreArmaSS->apply_transition($arr);
    //
    //   if ($res->code != 200) {
    //     return response()->json(['error'=>$res->message], 403);
    //   }
    //
    //    # return success response
    //    return response()->json(['success' => true, 'message'=>$res], $this->successStatus);
    // }

}
