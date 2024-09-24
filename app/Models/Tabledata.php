<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tabledata extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function getRaceTable()
    {
        return $this->belongsTo(Tabele::class, 'tabele_id');
    }

    public function getMannschaft()
    {
        return $this->belongsTo(RegattaTeam::class, 'mannschaft_id');
    }
}
