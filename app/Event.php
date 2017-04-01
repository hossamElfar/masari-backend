<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['title', 'description', 'venue', 'start', 'end', 'date'];

    protected $dates = ['date'];

    public function user()
    {
        $this->belongsTo('App\User');
    }
}
