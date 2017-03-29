<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = ['title', 'description', 'link', 'verified','user_id'];

    public function user()
    {
        $this->belongsTo('App\User');
    }
}
