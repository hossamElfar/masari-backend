<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = ['answer_content', 'points', 'question_id', 'user_id'];

    public function question()
    {
        return $this->belongsTo('App\Question');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function questionnaire()
    {
        return $this->belongsToMany('App\Questionnaire', 'assessments_answers');
    }

    public function values()
    {
        return $this->hasMany('App\Value');
    }

    public function grades()
    {
        return $this->hasMany('App\Grade');
    }
}
