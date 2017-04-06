<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['question_content', 'category', 'no_of_answers', 'questionnaire_id'];

    public function questionnaire()
    {
        return $this->belongsTo('App\Questionnaire');
    }

    public function answers()
    {
        return $this->hasMany('App\Answer');
    }

    public function values()
    {
        return $this->hasMany('App\Value');
    }
    
}
