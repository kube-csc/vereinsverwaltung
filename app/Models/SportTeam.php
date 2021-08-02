<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory; //todo: Wird das benötigt?
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class SportTeam extends Model
{
  //use HasFactory; //todo: Wird das benötigt?
  use SoftDeletes;

  protected $guarded = [];

  public function events()
  {
      return $this->hasMany(Event::class);
  }

  public function creator()
  {
      return $this->belongsTo(User::class, 'user_id');
  }

/*
  public function getImagePathAttribute()
    {
        return Storage::disk('public')->url($this->bild);
    }
*/

}
