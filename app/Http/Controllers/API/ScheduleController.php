<?php

namespace App\Http\Controllers\API;

use App\Field;
use App\Timing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use \Validator;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('getExperts', 'reserveExpert');
        $this->middleware('expert')->only('addTiming', 'requestedTiming', 'approvedTiming', 'approveTiming', 'getTimingsExpert');
        //$this->middleware('admin')->only('verify');
    }

    protected function validator(array $data)
    {
        //dd($data);
        return Validator::make($data, [
            'client_id' => 'unique_with:requests,expert_id,timing_id'
        ]);
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
                    if (($expert->country == Input::get('country')) && ($expert->city == Input::get('city'))&&($timing->reserved==false)) {
                        $timing['expert'] = $expert;
                        array_push($timings_free, $timing);
                    }
                }
            }
            $data['date'] = $date;
            $data['timings'] = $timings_free;
            array_push($returned, $data);
            $timings_free = [];
        }
        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data']['timings'] = $returned;
        return response()->json($data1, 200);
    }

    /**
     * Request to reserve a timing with an expert
     *
     * @param $timing_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reserveExpert($timing_id)
    {
        $timing = Timing::findOrFail($timing_id);
        $expert = $timing->expert()->get()[0];
        $user = Auth::user();
        $request = new \App\Request(['expert_id' => $expert->id, 'client_id' => $user->id, 'timing_id' => $timing->id, 'reserved' => false, 'accepted' => false]);
        $validator = $this->validator($request->toArray());

        if ($validator->fails())
            return response()->json($validator->errors(), 302);
        $request->save();
        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data'] = null;
        return response()->json($data1, 200);
    }

    protected function validatorUpdate(array $data)
    {
        return Validator::make($data, [
            'timing' => 'unique_with:timings,user_id'
        ]);
    }
    /**
     * Add a timing
     *
     * @param Request $request
     * @return mixed
     */
    public function addTiming(Request $request)
    {
        $data = $request->all();

        $user = Auth::user();
        $data['user_id'] = $user->id;
        $data['reserved'] = false;
        $validator = $this->validatorUpdate($data);
        if ($validator->fails())
            return response()->json($validator->errors(), 302);
        $timing = new Timing($data);
        $timing->save();
        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data'] = $timing;
        return $data1;
    }

    /**
     * Get the requested timings
     *
     * @return mixed
     */
    public function requestedTiming()
    {
        $user = Auth::user();
        $requested_timings = $user->request_expert()->where('reserved', '!=', true)->get();
        foreach ($requested_timings as $timing) {
            $timing['timing'] = $timing->timing()->get()[0];
        }
        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data']['timings'] = $requested_timings;
        return $data1;
    }

    /**
     * Get the approved timings
     *
     * @return mixed
     */
    public function approvedTiming()
    {
        $user = Auth::user();
        $approved_timings = $user->request_expert()->where('reserved', true)->get();
        foreach ($approved_timings as $timing) {
            $timing['timing'] = $timing->timing()->get()[0];
        }
        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data']['timings'] = $approved_timings;
        return $data1;
    }

    /**
     * Approve a request
     *
     * @param $request_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveTiming($request_id)
    {
        $request = \App\Request::findOrFail($request_id);
        $timing = Timing::find($request->timing_id);
        if ($request->reserved == true) {
            $data1['statues'] = "302";
            $data1['error'] = "Already reserved";
            $data1['data'] = null;
            return response()->json($data1, 302);
        } else {
            $request->reserved = true;
            $request->accepted = true;
            $timing->reserved = true;
            $request->save();
            $timing->save();
            $data1['statues'] = "200 Ok";
            $data1['error'] = null;
            $data1['data']['request'] = $request;
            return response()->json($data1, 200);
        }
    }

    public function getTimingsExpert()
    {
        $user = Auth::user();
        $returned = $user->timings()->get();
        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data']['timings'] = $returned;
        return response()->json($data1, 200);
    }

    public function getClientsApprovedMeetings()
    {
        $user = Auth::user();
        $requested_timings = $user->request_client()->where('reserved', true)->get();
        foreach ($requested_timings as $timing) {
            $timing['timing'] = $timing->timing()->get()[0];
        }
        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data']['timings'] = $requested_timings;
        return $data1;
    }

    public function getClientsRequestedMeetings()
    {
        $user = Auth::user();
        $requested_timings = $user->request_client()->where('reserved', false)->get();
        foreach ($requested_timings as $timing) {
            $timing['timing'] = $timing->timing()->get()[0];
        }
        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data']['timings'] = $requested_timings;
        return $data1;
    }
}
