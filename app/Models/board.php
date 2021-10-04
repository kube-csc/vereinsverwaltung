<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class board extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function boardUserName()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function boardUsers(){
        return $this->belongsToMany('App\Models\User');

        //return $this->belongsToMany('App\Hobby');
    }
}
