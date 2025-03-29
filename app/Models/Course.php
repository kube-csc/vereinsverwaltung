<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
   use SoftDeletes;

   protected $fillable = [];

   protected $dates = [];

    public function sportSection()
    {
         return $this->belongsToMany(SportSection::class);
    }

}
