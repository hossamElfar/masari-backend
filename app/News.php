<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['content', 'title', 'verified', 'user_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
