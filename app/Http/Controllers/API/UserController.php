<?php

namespace App\Http\Controllers\API;

use App\Questionnaire;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('show', 'getAssessmentsNames', 'getAssessment');;
    }

    /**
     * get user profile information
     * @return array
     */
    public function show()
    {
        $user = Auth::user();
        $data = array();
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['user'] = $user;
        $data['data']['no_of_assessments'] = $user->questioners()->count();
        return $data;
    }

    /**
     * get Assessments Names
     * @return mixed
     */
    public function getAssessmentsNames()
    {
        $user = Auth::user();
        $user_assessments = $user->questioners()->get();
        $assessments = DB::table('questionnaires')->select('name', 'id')->get();
        $flag = false;
        $returned = array();
        foreach ($assessments as $assessment) {
            $flag = false;
            foreach ($user_assessments as $user_assessment) {
                if ($assessment->id == $user_assessment) {
                    $flag = true;
                    break;
                }
            }
            if (!$flag) {
                array_push($returned, $assessment);
            }
        }
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['assessments'] = $returned;
        return $data;
    }

    /**
     * get Assessment questions
     * @param $id
     * @return mixed
     */
    public function getAssessment($id)
    {
        $assessment = Questionnaire::findOrFail($id);
        $questions = $assessment->questions()->paginate(5);
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        foreach ($questions as $question) {
            $answers = $question->answers()->get();
            $question['answers'] = $answers;
        }
        $data['data']['questions'] = $questions;
        return $data;
    }

    /**
     * Store assessment score
     * @param Request $request
     * @return mixed
     */
    public function storeAssessment(Request $request)
    {
        $data = $request->all();
        return $data;
    }
}
