<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class boardUser extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function boardUserName()
    {
        return $this->belongsTo(User::class, 'boardUser_id');
    }

    public function boardName()
    {
        return $this->belongsTo(User::class, 'board_id');
    }
}
