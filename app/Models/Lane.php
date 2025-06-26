<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lane extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    // ToDo: regattaTeam wird nicht verwendet
    public function regattaTeam()
    {
        return $this->belongsTo(RegattaTeam::class, 'mannschaft_id');
    }
    // ToDo: getTableLane wird nicht verwendet
    public function getTableLane()
    {
        return $this->belongsTo(Tabele::class, 'tabele_id');
    }
}
