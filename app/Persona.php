<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    // protected $attributes = ['numero_documento_identidad'=>null];

    public function personable(){
        return $this->morphTo();
    }

    public function domicilios()
    {
        return $this->hasMany(Domicilio::class);
    }

    public function abogado()
    {
        return $this->hasOne(PersonaAbogado::class);
    }
}
