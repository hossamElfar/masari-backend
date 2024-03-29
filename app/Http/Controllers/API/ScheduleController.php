<?php

namespace App\Http\Controllers\API;

use App\Field;
use App\Timing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use \Validator;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('getExperts', 'reserveExpert','getClientsApprovedMeetings','getClientsRequestedMeetings');
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
                    if (($expert->country == Input::get('country')) && ($expert->city == Input::get('city')) && ($timing->reserved == false) && ($timing->requested == false)) {
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
        $timing->requested = true;
        $timing->save();
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
     * @return \Illuminate\Http\JsonResponse
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
        return response()->json($data1, 200);
    }

    /**
     * Get the requested timings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestedTiming()
    {
        $user = Auth::user();
        $requested_timings = $user->request_expert()->where('reserved', '!=', true)->orderBy('created_at','desc')->get()
        ;
        foreach ($requested_timings as $timing) {
            $timing['timing'] = $timing->timing()->get()[0];
            $timing['requested_by'] = $timing->client()->get()[0];
        }
        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data']['timings'] = $requested_timings;
        return response()->json($data1, 200);
    }

    /**
     * Get the approved timings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function approvedTiming()
    {
        $user = Auth::user();
        $approved_timings = $user->request_expert()->where('reserved', true)->orderBy('created_at','desc')->get();
        foreach ($approved_timings as $timing) {
            $timing['timing'] = $timing->timing()->get()[0];
            $timing['requested_by'] = $timing->client()->get()[0];
        }
        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data']['timings'] = $approved_timings;
        return response()->json($data1, 200);
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
            $client = $request->client()->get()[0];
            $expert = $request->expert()->get()[0];
            Mail::send('auth.email.schedule', ['expert' => $expert, 'client' => $client, 'timing' => $timing->timing], function ($message) use ($client) {
                $message->to($client->email, $client->first_name)
                    ->subject('Meeting approval');
            });
            $data1['statues'] = "200 Ok";
            $data1['error'] = null;
            $data1['data']['request'] = $request;
            return response()->json($data1, 200);
        }
    }

    /**
     * Get The timings of an expert
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTimingsExpert()
    {
        $user = Auth::user();
        $returned = $user->timings()->orderBy('timing','desc')->get();
        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data']['timings'] = $returned;
        return response()->json($data1, 200);
    }

    /**
     * Get client's approved meetings
     *
     * @return \Illuminate\Http\JsonResponse
     */
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
        return response()->json($data1, 200);
    }

    /**
     * Get client's requested meetings
     *
     * @return \Illuminate\Http\JsonResponse
     */
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
        return response()->json($data1, 200);
    }

    /**
     * Cancel a request
     *
     * @param $request_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelTiming($request_id)
    {
        $request = \App\Request::find($request_id);
        $timing = $request->timing()->get()[0];
        $request->reserved = false;
        $request->accepted = false;
        $timing->requested = false;
        $timing->save();
        $request->save();
        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data'] = null;
        return response()->json($data1, 200);
    }

    /**
     * Remove a timing
     *
     * @param $timing_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeTiming($timing_id)
    {
        $timing = Timing::find($timing_id);
        $timing->requests()->delete();
        $timing->delete();
        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data'] = null;
        return response()->json($data1, 200);
    }
}
