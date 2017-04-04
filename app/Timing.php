<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Timing extends Model
{
    protected $fillable =['timing','user_id','reserved'];
    protected $table = 'timings';
    protected $dates = ['timing'];

    public function expert()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function requests()
    {
        return $this->hasMany('App\Request');
    }
}
