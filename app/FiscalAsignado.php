<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FiscalAsignado extends Model
{
    protected $table = "fiscales_asignados";
    protected $attributes = ['fecha_asignacion'=>null];

    protected $fillable = [
        "fecha_asignacion",
        "fiscalia_asignada_id",
        "fiscal_id",
        "imputado_id",
        "denuncia_id",
        "delito_atribuido_id",
        "sospechoso_id"
    ];

    public function fiscal()
    {
        return $this->belongsTo(Fiscal::class);
    }

    public function imputado()
    {
        return $this->belongsTo(Imputado::class);
    }

    public function sospechoso()
    {
        return $this->belongsTo(Sospechoso::class);
    }

    public function denuncia()
    {
        return $this->belongsTo(Denuncia::class);
    }

    public function delito_atribuido()
    {
        return $this->belongsTo(DelitoAtribuido::class, 'delito_atribuido_id');
    }

    public function fiscalia()
    {
        return $this->belongsTo(FiscaliaAsignada::class, 'fiscalia_asignada_id');
    }
}
