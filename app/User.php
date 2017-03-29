<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'email', 'password', 'second_name', 'phone', 'code', 'country', 'city', 'age', 'gender', 'pp',
        'pp', 'user_level'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function fileds()
    {
        return $this->belongsToMany('App\Field', 'fields_users');
    }

    public function grades()
    {
        return $this->hasMany('App\Grade');
    }

    public function links()
    {
        return $this->hasMany('App\Link');
    }

    public function messages()
    {
        return $this->hasMany('App\Message');
    }

    public function news()
    {
        return $this->hasMany('App\News');
    }

    public function pictures()
    {
        return $this->hasMany('App\Picture');
    }

    public function programs()
    {
        return $this->hasMany('App\Program');
    }

    public function qs()
    {
        return $this->hasMany('App\Q');
    }
    
    public function questioners()
    {
        return $this->hasMany('App\Questionnaire');
    }

    public function videos()
    {
        return $this->hasMany('App\Video');
    }
}
