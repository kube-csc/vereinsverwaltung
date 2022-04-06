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

    public function boardCradeName(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function boardEditName(){
        return $this->belongsTo(User::class, 'bearbeiter_id');
    }

    //ToDo: in der board.index.blase.php und boardController wird mit join in fuction index mit gearbeitet weil die function boardSection nicht funktioniert
    public function boardSection(){
        return $this->belongsTo(User::class, 'sportSection_id');
    }

}
