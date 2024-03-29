<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class botmanQuestion extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $guarded = [];

    public function user() {
        return $this->belongsTo('App\Models\user');
    }

    public function botmanAnswers() {
        return $this->belongsToMany('App\Models\botmanAnswer');
    }

    public function newBotmanQuestions() {
        return $this->belongsToMany('App\Models\newBotmanQuestion');
    }

}
