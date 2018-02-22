<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class DenunciaSS extends Model
{
  use WorkflowTrait;

  protected $table = "denuncias_ss";
  protected $attributes =  [
    'numero_denuncia'=>null,
    'workflow_state'=>null, 
    'recepcionada_en' => null,
    'funcionario_id' => null
  ];

  protected $fillable = [
  	"numero_denuncia",
  	"workflow_state",
    'recepcionada_en',
    'funcionario_id'
  ];

  public function institucion(){
      return $this->morphOne(Denuncia::class, 'institucionable');
  }

  public function formable(){
      return $this->morphTo();
  }

   public function recepcionada()
  {
      return $this->belongsTo(Dependencia::class, 'recepcionada_en');
  } 

  public function receptor()
  {
      return $this->belongsTo(Funcionario::class);
  }  
}
