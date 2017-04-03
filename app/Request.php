<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $table = 'requests';
    protected $fillable =['client_id','expert_id','timing_id','reserved','accepted'];

    public function client()
    {
        return $this->belongsTo('App\User', 'client_id');
    }

    public function expert()
    {
        return $this->belongsTo('App\User', 'expert_id');
    }

    public function timing()
    {
        return $this->belongsTo('App\Timing', 'timing_id');
    }
}
