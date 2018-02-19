<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HitoSS extends Model
{
    protected $table = "informesdelitoscomunes_ss";

    public function dictamen() {
         return $this->belongsTo(Dictamen::class, 'id_documento');
    }

    public function acta() {
         return $this->belongsTo(Acta::class, 'id_documento');
    }

    public function solicitud() {
         return $this->belongsTo(Solicitud::class, 'id_documento');
    }

    public function informe() {
         return $this->belongsTo(Informe::class, 'id_documento');
    }

    public function reporte() {
         return $this->belongsTo(Reporte::class, 'id_documento');
    }
}
