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
       return $this->hasMany(Event::class);
     }

     public function autor()
     {
         return $this->belongsTo(User::class, 'autor_id');
     }

     public function editor()
     {
         return $this->belongsTo(User::class, 'bearbeiter_id');
     }

     public function sportSectionName()
     {
         return $this->belongsTo(SportSection::class, 'sportSection_id');
     }

     public function eventGroupName()
     {
         return $this->belongsTo(eventGroup::class, 'eventGroup_id');
     }

     public function regattaTeams()
     {
         return $this->hasMany(\App\Models\RegattaTeam::class, 'regatta_id', 'id');
     }

}
