<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Denuncia extends Model
{
  protected $attributes =  ['fecha_denuncia'=>null, 'observaciones'=>null];

  protected $fillable = [
    "fecha_denuncia",
    "hora_denuncia",
    "observaciones",
  ];

  public function institucionable(){
      return $this->morphTo();
  }

  public function documento(){
      return $this->morphOne(Documento::class, 'documentable');
  }

  public function anexos() {
      return $this->hasMany(Anexo::class);
  }

  public function hechos()
  {
      return $this->hasMany(Hecho::class);
  }

    public function denunciantes()
    {
        return $this->hasMany(Denunciante::class);
    }

  public function denunciados()
  {
      return $this->hasMany(Denunciado::class);
  }

  public function ofendidos()
  {
      return $this->hasMany(Ofendido::class);
  }

  public function sospechosos()
  {
      return $this->hasMany(Sospechoso::class);
  }

  public function victimas()
  {
      return $this->hasMany(Victima::class);
  }

  public function testigos()
  {
      return $this->hasMany(Testigo::class);
  }

  public function imputados()
  {
      return $this->hasMany(Imputado::class);
  }

  public function fiscales()
  {
      //return $this->hasMany(FiscalAsignado::class);
      return $this->belongsToMany(Fiscal::class, 'fiscales_asignados');
  }

  public function fiscalias()
  {
      return $this->hasMany(FiscaliaAsignada::class);
  }

  public function delitos()
  {
      return $this->hasMany(DelitoAtribuido::class);
  }

}
