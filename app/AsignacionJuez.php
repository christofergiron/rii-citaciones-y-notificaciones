<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class AsignacionJuez extends Model
{
    use WorkflowTrait;

	protected $table = "asiganciones_juez";

	protected $fillable = [
		'juez_id',
		'proceso_judicial_id',
		'tipo_audiencia',
		'id_audiencia',
		'workflow_state', 
		'numero_linea', 
		'razon',
		'correo_electronico'
	];

	public function juezAsignado()
	{
	    return $this->belongsTo(Juez::class);
	}

	public function procesoJudicial()
	{
	    return $this->belongsTo(ProcesoJudicial::class);
	}

}
