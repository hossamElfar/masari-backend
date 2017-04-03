<?php

namespace App\Http\Controllers\API;

use App\Field;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('getExperts');
        //$this->middleware('expert')->only('store', 'update', 'destroy');
        //$this->middleware('admin')->only('verify');
    }

    public function getExperts($field_id)
    {
        $auth_user = Auth::user();
        $field = Field::findOrFail($field_id);
        $users = $field->user()->where('country', $auth_user->country)->get();
        return $users;
    }
}
