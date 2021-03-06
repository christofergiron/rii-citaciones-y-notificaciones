<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolicitudRecordHistorial extends Model
{
    protected $fillable = [      
      "workflow_state"
    ];
    protected $table = "solicitudesrecordhistoriales_ss";

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }
}
