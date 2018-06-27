<?php

namespace App\Http\Controllers\API;

use App\Celular;
use App\Helpers\BuilderTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\CelularRequest;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;

class CelularController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $celulares = Celular::all();

        $builder = new BuilderTable();
       $table= $builder->newTable()
            ->addHeader("id","Identificador")
            ->addHeader("marca","Marca")
            ->addHeader("modelo","Modelo")
            ->addHeader("imei","IMEI")
            ->addHeader("created_at","Fecha reación")
            ->addHeader("updated_at","Fecha actualización")
            ->buildRows($celulares)
            ->buildTable();

        return response()->json($table);
    }


    public function store(CelularRequest $request)
    {
        $id = Celular::create($request->all())->id;
    return response()->json(['success'=>'true',"id"=>$id]);
    }

    public function show(Request $request)
    {
        $result = Celular::find($request['id']);
        return  response()->json( isset($result)?$result:['error'=>'No encontrado']);
    }


    public function edit(CelularRequest $request)
    {
        $result = Celular::whereId($request['id']);

        if($result->count()==0)
            return  response()->json( ['error'=>'No actualizado']);


        $result->update($request->all());
        return  response()->json(['sucess'=>true]);
    }


}
