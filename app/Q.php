<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Q extends Model
{
    protected $table = 'qs';
    protected $fillable = ['question', 'user_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function answers()
    {
        return $this->hasMany('App\A','question_id');
    }
}
