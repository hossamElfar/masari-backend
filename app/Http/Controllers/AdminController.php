<?php

namespace App\Http\Controllers;

use App\A;
use App\Answer;
use App\Event;
use App\News;
use App\Program;
use App\Q;
use App\Questionnaire;
use App\User;
use App\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Bouncer;
use Validator;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function getUnverifiedNews()
    {
        $news = News::all();
        foreach ($news as $n) {
            $n['by'] = $n->user()->get()[0];
        }
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['news'] = $news;
        return response()->json($data, 200);
    }

    public function getUnverifiedVideos()
    {
        $news = Video::all();
        foreach ($news as $n) {
            $n['by'] = $n->user()->get()[0];
        }
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['videos'] = $news;
        return response()->json($data, 200);
    }

    public function getUnverifiedEvents()
    {
        $news = Event::all();
        foreach ($news as $n) {
            if ($n->user() != null) {
                $n['by'] = $n->user()->get();
            }
        }
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['events'] = $news;
        return response()->json($data, 200);
    }

    public function getUnverifiedPrograms()
    {
        $news = Program::all();
        foreach ($news as $n) {
            $n['by'] = $n->user()->get()[0];
        }
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['programs'] = $news;
        return response()->json($data, 200);
    }

    public function getUnverifiedQuestions()
    {
        $news = Q::all()->sortByDesc('created_at')->values();
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        foreach ($news as $question) {
            $question1 = Q::find($question->id);
            $answers = $question1->answers()->count();
            $question->no_of_answers = $answers;
            $question->asked_by = $question1->user()->get();
        }
        $data['data']['questions'] = $news;
        return response()->json($data, 200);
    }

    public function getUnverifiedQuestionAndAnswers($id)
    {
        $news = Q::find($id);
        $news['asked_by'] = $news->user()->get();
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $answers = $news->answers()->get();
        foreach ($answers as $answer) {
            $answer['answered_by'] = $answer->user()->get()[0];
        }
        $news['answers'] = $answers;
        $data['data']['question'] = $news;
        if ($news == null) {
            return response()->json($data, 404);
        } else {
            return response()->json($data, 200);
        }
    }

    public function verifyAnswer($id)
    {
        $answer = A::find($id);
        if ($answer == null) {
            $data['statues'] = "404 not found";
            $data['error'] = "404";
            $data['data'] = null;
            return response()->json($data, 404);
        }
        $answer->verified = true;
        $answer->save();
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data'] = null;
        return response()->json($data, 200);
    }

    public function getUserAssessments($user_code)
    {
        $user_db = DB::table('users')->where('code', $user_code)->first();
        $user = User::find($user_db->id);
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['assessments'] = $user->questioners()->get();
        return response()->json($data, 200);
    }

    public function removeUserAssessments($user_code)
    {
        $user_db = DB::table('users')->where('code', $user_code)->first();
        $user = User::find($user_db->id);
        $assessment_id = Input::get('assessment_id');
        $q = Questionnaire::find($assessment_id);
        $q->user()->detach($user);
        $q->grades()->where('user_id', $user->id)->delete();
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data'] = null;
        return response()->json($data, 200);
    }

    public function registerLow(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails())
            return response()->json($validator->errors(), 302);
        $user = $this->create($request->all());
        $user->assign('ladmin');
        return response()->json(['message' => '200 Ok', 'confirmation' => $user->code], 200);
    }

    protected function create(array $data)
    {
        $confirmation_code_init = str_random(5);
        $confirmation_code = strtolower($confirmation_code_init);
        $user = new User();
        try {
            $user = User::create([
                'first_name' => $data['first_name'],
                'second_name' => $data['second_name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'country' => $data['country'],
                'city' => $data['city'],
                'gender' => $data['gender'],
                'birth_date' => $data['birth_date'],
                'password' => bcrypt($data['password']),
                'code' => $confirmation_code,
                'user_level' => "0",
                'confirmed' => true
            ]);
        } catch (\Exception $e) {
            throw $e;
            //$this->create($data);
        }
        return $user;
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|max:255',
            'second_name' => 'required|max:255',
            'phone' => 'required|max:20',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
            'birth_date' => 'required',
            'gender' => 'required',
            'city' => 'required',
            'country' => 'required'
        ]);
    }

    public function registerHigh(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails())
            return response()->json($validator->errors(), 302);
        $user = $this->create($request->all());
        $user->assign('hadmin');
        return response()->json(['message' => '200 Ok', 'confirmation' => $user->code], 200);
    }

    /**
     * Assign Expert 
     *
     * @param $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignExpert($user_id)
    {
        $user = User::find($user_id);
        $user->assign('expert');
        $user->save();
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data'] = null;
        return response()->json($data, 200);
    }
}
