<?php

namespace App\Http\Controllers;

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
        foreach ($news as $n){
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
        foreach ($news as $n){
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
        foreach ($news as $n){
            $n['by'] = $n->user()->get()[0];
        }
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['events'] = $news;
        return response()->json($data, 200);
    }
    public function getUnverifiedPrograms()
    {
        $news = Program::all();
        foreach ($news as $n){
            $n['by'] = $n->user()->get()[0];
        }
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['programs'] = $news;
        return response()->json($data, 200);
    }
}
