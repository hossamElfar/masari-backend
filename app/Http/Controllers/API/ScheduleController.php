<?php

namespace App\Http\Controllers\API;

use App\Field;
use App\Timing;
use Carbon\Carbon;
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
        $day = Carbon::now();
        $days = array();
        $dates = array();
        $experts = array();
        $timings_free = array();
        $returned = array();
        for ($date = Carbon::now(); $date->lte(Carbon::now()->addDays(6)); $date->addDay()) {
            array_push($dates, $date->format('Y-m-d'));
        }
        // dd($dates);
        $timings = Timing::all();

        foreach ($dates as $date) {
            foreach ($timings as $timing) {
                $date_temp = Carbon::parse($date)->format('Y-m-d');
                // dd($timing);
                $timing_temp = Carbon::parse($timing->timing)->format('Y-m-d');
                if ($date_temp == $timing_temp) {
                    $expert = $timing->expert()->get()[0];
                    $timing['expert'] = $expert;
                    array_push($timings_free, $timing);
                }
            }
            $data['date'] = $date;
            $data['timings'] = $timings_free;
            array_push($returned, $data);
            $timings_free = [];
        }
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['timings'] = $returned;
        return response()->json($data, 200);
    }
}