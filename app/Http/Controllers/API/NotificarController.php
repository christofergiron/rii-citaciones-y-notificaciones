<?php

namespace App\Http\Controllers\API;

use App\Tools;
use App\AlertaAmberTools;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Requests\StoreNotificar;


class NotificarController extends Controller
{
    public $successStatus = 200;
    private $root;
    private $StoreNotificar;

    public function __construct(LoggerInterface $logger)
    {
        $this->log = new \Log;
        $this->logger = $logger;
        $this->tools = new Tools($this->logger);
        $this->root = false;
        $this->StoreNotificar = new StoreNotificar;
    }
    public function store(Request $request)
    {
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
        $res = $this->StoreNotificar->rules($arr);

        if ($res->code != 200) {
            return response()->json(['error'=>$res->message], 403);
        }

        $res = $this->StoreNotificar->persist($arr);

        # return success response
        return response()->json(['message'=>$res], $this->successStatus);
    }
}
