<?php

namespace App\Http\Controllers\API;

use App\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Bouncer;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('index', 'show');
        $this->middleware('expert')->only('store', 'update', 'destroy');
        $this->middleware('admin')->only('verify');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Bouncer::is(Auth::user())->an('admin', 'admin_level_1', 'admin_level_2','expert')) {
            $news = DB::table('news')->orderBy('created_at','desc')->paginate(7);
            $data['statues'] = "200 Ok";
            $data['error'] = null;
            $data['data']['news'] = $news;
            return response()->json($data, 200);
        } else {
            $news = DB::table('news')->where('verified', true)->orderBy('created_at','desc')->paginate(7);
            $data['statues'] = "200 Ok";
            $data['error'] = null;
            $data['data']['news'] = $news;
            return response()->json($data, 200);
        }
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
        $t['user_id'] = Auth::user()->id;
        $news = new News($t);
        $news['verified']=false;
        $news['user_id'] = Auth::user()->id;
        $news->save();
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['news'] = $news;
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
        if (Bouncer::is(Auth::user())->an('admin', 'admin_level_1', 'admin_level_2')) {
            $news = News::findOrFail($id);
            $data['statues'] = "200 Ok";
            $data['error'] = null;
            $data['data']['news'] = $news;
            return response()->json($data, 200);
        } else {
            $news = DB::table('news')->where('verified', true)->where('id', $id)->get()->first();
            $data['statues'] = "200 Ok";
            $data['error'] = null;
            $data['data']['news'] = $news;
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
        $news = News::findOrFail($id);
        if ($news->user_id != Auth::user_id && Bouncer::is(Auth::user())->notAn('admin')) {
            $data['statues'] = "401 unauthorized";
            $data['error'] = "UnAuthorized";
            $data['data'] = null;
            return response()->json($data, 401);
        }
        $t->user_id = Auth::user()->id;
        $news->update($request);
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['news'] = $news;
        return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $news = News::findOrFail($id);
        if ($news->user_id != Auth::user_id && Bouncer::is(Auth::user())->notAn('admin')) {
            $data['statues'] = "401 unauthorized";
            $data['error'] = "UnAuthorized";
            $data['data'] = null;
            return response()->json($data, 401);
        }
        $news->destroy();
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data'] = null;
        return response()->json($data, 200);
    }

    /**
     * Verify a news posted by an expert.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function verify($id)
    {
        $news = News::findOrFail($id);
        $news->verified = true;
        $news->save();
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data'] = $news;
        return response()->json($data, 200);
    }
}
