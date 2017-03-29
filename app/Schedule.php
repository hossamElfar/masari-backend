<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = ['content', 'user_id', 'expert_id', 'confirmation', 'field_id'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function expert()
    {
        return $this->belongsTo('App\User', 'expert_id');
    }

    public function field()
    {
        return $this->belongsTo('App\Field', 'field_id');
    }
}
