<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['title', 'note', 'link', 'verified', 'user_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
