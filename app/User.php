<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Cmgmyr\Messenger\Traits\Messagable;

class User extends Authenticatable
{
    use HasRolesAndAbilities;
    use Notifiable;
    use Messagable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'email', 'password', 'second_name', 'phone', 'code', 'country', 'city', 'age', 'gender', 'pp',
        'pp', 'user_level','field_id'
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
        return $this->belongsTo('App\Questionnaire', 'questionnaires_users');
    }

    public function videos()
    {
        return $this->hasMany('App\Video');
    }

    public function events()
    {
        return $this->hasMany('App\Event');
    }
}
