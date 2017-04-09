<?php

namespace App\Http\Controllers;

use App\News;
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
        $news = DB::table('news')->get();
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['news'] = $news;
        return response()->json($data, 200);
    }
    public function getUnverifiedVideos()
    {
        $news = DB::table('videos')->get();
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['videos'] = $news;
        return response()->json($data, 200);
    }
    public function getUnverifiedEvents()
    {
        $news = DB::table('events')->get();
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['events'] = $news;
        return response()->json($data, 200);
    }
    public function getUnverifiedPrograms()
    {
        $news = DB::table('programs')->get();
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['programs'] = $news;
        return response()->json($data, 200);
    }
}
