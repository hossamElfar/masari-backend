<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Value extends Model
{
    protected $fillable = ['answer_content', 'points', 'user_id', 'question_id', 'questionnaire_id', 'answer_id','rank'];

    public function question()
    {
        return $this->belongsTo('App\Question');
    }

    public function questionnaire()
    {
        return $this->belongsTo('App\Questionnaire');
    }

    public function answer()
    {
        return $this->belongsTo('App\Answer');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
