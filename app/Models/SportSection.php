<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory; //todo: Wird das benötigt?
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class SportSection extends Model
{
    //use HasFactory; //todo: Wird das benötigt?
    use SoftDeletes;

    protected $guarded = [];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //public function event() {
      //  return $this->belongsTo('App\Models\event');
  //  }

    public function getImagePathAttribute()
      {
          return Storage::disk('public')->url($this->bild);
      }
}
