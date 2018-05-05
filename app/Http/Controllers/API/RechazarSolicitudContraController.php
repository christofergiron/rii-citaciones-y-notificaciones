<?php

namespace App\Http\Controllers\API;

use App\Fiscal;
use App\FuncionarioSS;
use App\DenunciaSS;
use App\PersonaNatural;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Requests\StoreRechazarSolicitudContraOrden;

class RechazarSolicitudContraController extends Controller
{

    public  $successStatus = 200;
    private $root;
    private $solicitud_tools;
    private $StoreSolicitud;

    public function __construct(LoggerInterface $logger)
    {
        $this->log = new \Log;
        $this->logger = $logger;
        $this->root = false;
        $this->StoreSolicitud = new StoreRechazarSolicitudContraOrden;
    }

    public function store(Request $request) {
      $arr = $request->all();

      $header = $request->header('Authorization');
      if ($header=='') {
          $header =  $request->all()['token'];
      }
      $header = str_replace('Bearer ', '', $header);
      $arr['token'] = $header;
      
      $this->logger->alert('inside Store solicitud');
      $this->logger->alert(json_encode($arr)) ;
      $res = $this->StoreSolicitud->rules($arr);

      if ($res->code != 200) {
        return response()->json(['error'=>$res->message], 403);
      }

      $res = $this->StoreSolicitud->persist($arr);

      if (!$res->success) {
        return response()->json(['error'=>$res->message], 403);
      }
       # return success response
       return response()->json(['success' => $res->success, 'message'=>$res->message, 'payload' => $res->payload], $this->successStatus);
    }


}
