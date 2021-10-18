<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class boardUser extends Model
{
    use HasFactory;

    public function boardUserName()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function boardName(){
        return $this->belongsToMany('App\Models\Board');
    }
}
