<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Questionnaire extends Model
{
    protected $fillable = ['name', 'no_of_questions', 'language'];

    public function answers()
    {
        return $this->belongsToMany('App\Answer', 'assessments_answers');
    }

    public function user()
    {
        return $this->belongsToMany('App\User', 'questionnaires_users');
    }

    public function questions()
    {
        return $this->hasMany('App\Question');
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
