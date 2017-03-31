<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = ['answer_content', 'points', 'question_id'];

    public function question()
    {
        return $this->belongsTo('App\Question');
    }

    public function questionnaire()
    {
       return $this->belongsToMany('App\Questionnaire','assessments_answers');
    }
}
