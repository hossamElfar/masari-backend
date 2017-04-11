<?php

namespace App\Http\Controllers\API;

use App\A;
use App\Q;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Bouncer;
use Illuminate\Support\Facades\DB;

class QsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('index', 'store', 'show', 'update', 'destroy');
        $this->middleware('expert')->only('addAnswer', 'updateAnswer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $news = DB::table('qs')->orderBy('created_at', 'desc')->paginate(7)->toArray();
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        foreach ($news['data'] as $question) {
            $question1 = Q::find($question->id);
            if ($user->isNotA('client')){
                $answers = $question1->answers()->count();
            }else{
                $answers = $question1->answers()->where('verified',true)->count();
            }
            $question->no_of_answers = $answers;
            $question->asked_by = $question1->user()->get();
        }
        $data['data']['questions'] = $news;
        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $t = $request->all();
        $t['verified'] = false;
        //$t['user_id'] = Auth::user()->id;
        $news = new Q($t);
        //$news['verified']=false;
        $news['user_id'] = Auth::user()->id;
        $news->save();
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['question'] = $news;
        return response()->json($data, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $news = Q::find($id);
        $news['asked_by'] = $news->user()->get();
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $user = Auth::user();
        if ($user->isAn('expert', 'admin', 'ladmin', 'hadmin')) {
            $answers = $news->answers()->get();
        } else {
            $answers = $news->answers()->where('verified', true)->get();
        }
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


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $t = $request->all();
        $news = Q::find($id);
        if ($news->user_id != Auth::user()->id) {
            $data['statues'] = "401 unauthorized";
            $data['error'] = "UnAuthorized";
            $data['data'] = null;
            return response()->json($data, 401);
        }
        $t['user_id'] = Auth::user()->id;
        if ($news == null) {
            $data['statues'] = "404 not found";
            $data['error'] = "Not found";
            $data['data'] = null;
            return response()->json($data, 404);
        } else {
            $news->update($t);
            $data['statues'] = "200 Ok";
            $data['error'] = null;
            $data['data']['question'] = $news;
            return response()->json($data, 200);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $news = Q::find($id);
        if ($news->user_id != Auth::user()->id) {
            $data['statues'] = "401 unauthorized";
            $data['error'] = "UnAuthorized";
            $data['data'] = null;
            return response()->json($data, 401);
        }
        if ($news == null) {
            $data['statues'] = "404 not found";
            $data['error'] = "Not found";
            $data['data'] = null;
            return response()->json($data, 404);
        } else {
            $news->destroy();
            $data['statues'] = "200 Ok";
            $data['error'] = null;
            $data['data'] = null;
            return response()->json($data, 200);
        }

    }

    /**
     * Add an answer to a question.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function addAnswer(Request $request, $id)
    {
        $t = $request->all();
        $t['verified'] = false;
        $t['question_id'] = $id;
        $t['user_id'] = Auth::user()->id;
        $answer = new A($t);
        $answer->save();
        $question = Q::findOrFail($id);
        $question->answers()->save($answer);
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['question'] = $question;
        $data['data']['question']['answers'] = $question->answers()->get();
        return response()->json($data, 200);
    }

    /**
     * Update an answer to a question.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function updateAnswer(Request $request, $answer_id)
    {
        $t = $request->all();
        $answer = A::findOrFail($answer_id);
        $answer->update($t);
        $question = $answer->question()->get()[0];
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['question'] = $answer->question()->get();
        $data['data']['question']['answers'] = $question->answers()->get();
        return response()->json($data, 200);
    }


}
