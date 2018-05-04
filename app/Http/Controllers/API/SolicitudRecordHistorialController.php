<?php

namespace App\Http\Controllers\API;

use App\Tools;
use App\SolicitudRecordHistorial;
use App\SolicitudRecordHistorialTools;
use App\Http\Requests\StoreSolicitudRecordHistorial;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;

class SolicitudRecordHistorialController extends Controller
{

    public  $successStatus = 200;
    private $root;
    private $solicitud_record_historial_tools;
    private $StoreSolicitudRecordHistorial;

    public function __construct(LoggerInterface $logger)
    {
        $this->log = new \Log;
        $this->logger = $logger;
        $this->tools = new Tools($this->logger);
        $this->solicitud_record_historial_tools = new SolicitudRecordHistorialTools;
        $this->root = false;
        $this->StoreSolicitudRecordHistorial = new StoreSolicitudRecordHistorial;
    }

    public function index(Request $request){
      $arr = $request->all();

      $header = $request->header('Authorization');
      if ($header=='') {
          $header =  $request->all()['token'];
      }
      $header = str_replace('Bearer ', '', $header);
      $arr['token'] = $header;

       #get denuncia_mp
       $res = $this->solicitud_record_historial_tools->ss_list_solicitudes($arr["token"]);

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

      $header = $request->header('Authorization');
      if ($header=='') {
          $header =  $request->all()['token'];
      }
      $header = str_replace('Bearer ', '', $header);
      $arr['token'] = $header;

      $this->log::alert($arr);

       #get denuncia_mp
       $res = $this->solicitud_record_historial_tools->get_solicitud($id, $arr["token"]);

       # chek for nulls
       if (is_null($res)) {
         return response()->json(['error'=>'No Content due denuncia mp is invalid!'], 403);
       }

       # return success response
       return response()->json(['success' => true, 'message'=>json_decode($res)], $this->successStatus);
    }

    public function store(Request $request) {
      $arr = $request->all();

      $header = $request->header('Authorization');
      if ($header=='') {
          $header =  $request->all()['token'];
      }
      $header = str_replace('Bearer ', '', $header);
      $arr['token'] = $header;

      $this->logger->alert('inside Store Solicitud Record Historial');
      $this->log::alert(json_encode($arr));

      $res = $this->StoreSolicitudRecordHistorial->rules($arr);

      if ($res->code != 200) {
        return response()->json(['error'=>$res->message], 403);
      }

      $res = $this->StoreSolicitudRecordHistorial->persist($arr);

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

      $res = $this->StoreSolicitudRecordHistorial->workflow_rules($arr);

      if ($res->code != 200) {
        return response()->json(['error'=>$res->message], 403);
      }

      //$res = $this->StoreDenunciaMP->asignar_delitos($arr);
      $res = $this->StoreSolicitudRecordHistorial->apply_transition($arr);

      if ($res->code != 200) {
        return response()->json(['error'=>$res->message], 403);
      }

       # return success response
       return response()->json(['success' => true, 'message'=>$res], $this->successStatus);
    }

}
