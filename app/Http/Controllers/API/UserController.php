<?php

namespace App\Http\Controllers\API;

use App\Field;
use App\Questionnaire;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Bouncer;
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
    /**
     * Get experts .
     *
     * @return \Illuminate\Http\Response
     */
    public function experts()
    {
        $query = Input::get('field');
        $returned = array();
        if ($query == 'all') {
            $users = User::all();
            //$returned = null;
            foreach ($users as $user) {
                if (Bouncer::is($user)->a('expert')) {
                    $user['fields'] = $user->fileds()->get();
                    array_push($returned, $user);
                }
            }
        } else {
            $field = Field::findOrFail($query);
            $users = $field->users();
            foreach ($users as $user) {
                $user['fields'] = $user->fileds()->get();
                array_push($returned, $user);
            }
        }
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['experts'] = $returned;
        return response()->json($data, 200);

    }

    /**
     * Get fields.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFields()
    {
        $fields = Field::all();
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data'] = $fields;
        return response()->json($data, 200);
    }
}
