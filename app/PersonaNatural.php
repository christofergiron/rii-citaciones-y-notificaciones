<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonaNatural extends Model
{
  protected $table = "personas_naturales";

  protected $fillable = [
    'genero',
    'sexo',
    'nacionalidad',
    'tipo_documento_identidad',
    'numero_documento_identidad',    
    'nombres',
    'primer_apellido',
    'segundo_apellido',
    'fecha_nacimiento',
    'edad',
    'estado_civil',
    'ocupaciones',
    'profesiones',
    'etnias',
    'escolaridad',
    'discapacidades',
    'pueblo_indigena',
    'telefonos',
    'correos_electronicos',
    'apartados_postales',
    'telefono_notificacion',
    'correo_notificacion',
    'apartado_postal_notificacion',
    'alias'
  ];

  protected $casts = [
      'apartados_postales' => 'array',
      'correos_electronicos' => 'array',
      'escolaridad' => 'array',
      'discapacidades' => 'array',
      'pueblo_indigena' => 'array',
      'etnias' => 'array',
      'ocupaciones' => 'array',
      'profesiones' => 'array',
      'telefonos' => 'array',
      'alias' => 'array'
  ];

  public function persona(){
      return $this->morphOne(Persona::class, 'personable');
  }

  public function institucionable(){
      return $this->morphTo();
  }

  public function identificable(){
      return $this->morphTo();
  }

  public function roles()
  {
      return $this->hasMany(Rol::class, 'persona_natural_id', 'id');
  }

  public function menor()
  {
      return $this->hasMany(PersonaMenor::class, 'persona_natural_id', 'id');
  }

  public function representante_menores()
  {
      return $this->hasMany(PersonaMenor::class, 'representante_legal_id', 'id');
  }

}
