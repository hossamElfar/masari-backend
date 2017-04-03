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

    /**
     * Getting the experts os a certain field in the user's country
     *
     * @param $field_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getExperts($field_id)
    {
        $auth_user = Auth::user();
        $field = Field::findOrFail($field_id);
        $users = $field->user()->where(['country' => $auth_user->country, 'city' => $auth_user->city])->get();
        foreach ($users as $user) {
            $user['timings'] = $user->timings()->get();
        }
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['field'] = $field;
        $data['data']['experts'] = $users;
        return response()->json($data, 200);
    }
}
