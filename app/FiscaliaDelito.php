<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FiscaliaDelito extends Model
{
  protected $table = "fiscalias_delitos";

  public function delito()
  {
      return $this->belongsTo(Delito::class);
  }

  public function fiscalia()
  {
      return $this->belongsTo(Fiscalia::class);
  }
}
