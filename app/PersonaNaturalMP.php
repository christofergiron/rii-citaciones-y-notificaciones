<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonaNaturalMP extends Model
{
  protected $table = "personas_naturales_mp";
  protected $attributes =  ['categorias'=>null,'tipo_identificacion'=>null,
                            'alias'=>null];
  protected $casts = [
      'categorias' => 'array',
      'tipo_identificacion' => 'array',
      'alias' => 'array'
  ];

  protected $fillable = [
    'categorias',
    'tipo_identificacion',
    'alias'
  ];
  
  public function institucion(){
      return $this->morphOne(PersonaNatural::class, 'institucionable');
  }

  public function direcciones()
  {
      return $this->hasMany(LugarMP::class, 'persona_natural_mp_id', 'id');
  }

}
