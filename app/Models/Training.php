<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Training extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'sportSection_id',
        'course_id',
        'organiser_id',
        'datumvon',
        'datumbis',
        'datumAktuell',
        'zeitvon',
        'zeitbis',
        'sportgeraeteanzahl',
        'sportgeraeteGebucht',
        'vorschauTage',
        'wiederholung',
        'autor_id',
        'bearbeiter_id',
    ];

    protected $dates = [
        'deleted_at',
        'updated_at',
        'created_at'
    ];

    public function getCourse()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
