<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class botmanAnswer extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $guarded = [];


    public function user() {
        return $this->belongsTo('App\Models\user');
    }

    public function botmanQuestions() {
        return $this->belongsToMany('App\Models\botmanQuestion');
    }
}
