<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instruction extends Model
{
    use SoftDeletes;

    /**
     * Hinweis: `instructions` enthält u.a. optionale Darstellungsfelder wie `headerBild` und `accentColor`.
     * Da das Projekt historisch stark dynamisch ist, bleibt `guarded=[]` bestehen.
     */

    protected $guarded = [];
}
