<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = ['score', 'user_id', 'answer_id', 'questionnaire_id', 'category'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function questionnaire()
    {
        return $this->belongsTo('App\Questionnaire');
    }

    public function answer()
    {
        return $this->belongsTo('App\Answer');
    }
}
