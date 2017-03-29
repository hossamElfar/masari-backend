<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = ['title', 'description', 'from', 'to', 'verified', 'user_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
