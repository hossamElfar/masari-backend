<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = ['score'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function questionnaire()
    {
        return $this->belongsTo('App\Questionnaire');
    }
}
