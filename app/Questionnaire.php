<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Questionnaire extends Model
{
    protected $fillable = ['name', 'no_of_questions'];

    public function answers()
    {
        return $this->belongsToMany('App\Answer','assessments_answers');
    }
}
