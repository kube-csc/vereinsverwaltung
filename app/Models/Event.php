<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function events()
     {
       return $this->hasMany(Events::class);
     }

     public function autor()
     {
         return $this->belongsTo(User::class, 'user_id');
     }

     public function editor()
     {
         return $this->belongsTo(User::class, 'user_id');
     }

}
