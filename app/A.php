<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class A extends Model
{
    protected $table = 'as';
    protected $fillable = ['answer', 'verified', 'question_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function question()
    {
        return $this->belongsTo('App\Q','question_id');
    }
}
