<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonaAbogado extends Model
{
   protected $table = "personas_abogados";
   protected $attributes = ["identificacion_colegio_abogados" => null];

   protected $fillable = [
   	'persona_id',
   	'identificacion_colegio_abogados'
   ];

   public function persona()
   {
       return $this->belongsTo(Persona::class);
   }
}
