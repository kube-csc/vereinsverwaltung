<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trainertyp extends Model
{
    use SoftDeletes;

    protected $table = 'trainertyps';

    protected $fillable = [
        'trainerfunktion',
        'status',
        'default_sichtbar',
        'organiser_id',
    ];

    protected $casts = [
        'status' => 'integer',
        'default_sichtbar' => 'integer',
        'organiser_id' => 'integer',
        'deleted_at' => 'datetime',
    ];

    public function trainerZuordnungen()
    {
        return $this->hasMany(Trainertable::class, 'trainertyp_id');
    }
}

