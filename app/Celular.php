<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Celular extends Model
{
    protected $table = "celulares";
    protected $fillable= ['id_marca','modelo','imei'];

    public function  detalle_catalgo(){
        return $this->hasOne(DetalleListaValorRelacional::class,"id_detalle_catalago","id_marca")->where('id_principal_catalago','=','1');
    }



}
