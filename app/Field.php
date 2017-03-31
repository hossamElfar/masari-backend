<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $fillable = ['field_name'];

    public function user()
    {
        return $this->belongsToMany('App\User', 'fields_users');
    }
}
