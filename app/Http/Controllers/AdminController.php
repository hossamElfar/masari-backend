<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Event;
use App\News;
use App\Program;
use App\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $news = DB::table('qs')->orderBy('created_at', 'desc');
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        foreach ($news['data'] as $question) {
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
        $answer = Answer::find($id);
        if ($answer == null){
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
}
