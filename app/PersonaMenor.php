<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonaMenor extends Model
{
    protected $table = "personas_menores";

    protected $fillable = [
    	'persona_natural_id',
    	'representante_legal_id'
    ];

    public function persona()
    {
        return $this->belongsTo(PersonaNatural::class, 'persona_natural_id');
    }

    public function representante_legal()
    {
        return $this->belongsTo(PersonaNatural::class, 'representante_legal_id');
    }
}
