<?php

namespace App\Http\Controllers\API;

use App\Answer;
use App\Field;
use App\Grade;
use App\Question;
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
        $this->middleware('auth')->only('show', 'getAssessmentsNames', 'getAssessment', 'storeAssessment');
        $this->middleware('expert')->only('getScore', 'getAnswers');
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
        $assessments = DB::table('questionnaires')->where('language', Input::get('language'))->select('name', 'id', 'language','description')->get();
        $flag = false;
        $returned = array();
        foreach ($assessments as $assessment) {
            $flag = false;
            foreach ($user_assessments as $user_assessment) {
                if ($assessment->id == $user_assessment->id) {
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
        $user = Auth::user();
        $data = $request->all();
        $returned = [];
        foreach ($data as $category) {
            $returned[$category['category']] = 0;
        }
        //  dd($data);
        foreach ($data as $answer) {

            $question = Question::find($answer['question_id']);
            $answer_db = Answer::find($answer['answer_id']);
            $questionnare = Questionnaire::find($answer['questionnare_id']);
            // dd($questionnare);
            // $answer_db['user_id']= $user->id;
            //$user->answers()->save($answer_db);
            $questionnare->answers()->save($answer_db, ["user_id" => $user->id]);
            $points = $answer['points'];
            $grade = new Grade(['user_id' => $user->id, 'answer_id' => $answer_db->id, 'questionnaire_id' => $questionnare->id, 'score' => $points, 'category' => $answer['category']]);
            $grade->save();
            $returned[$answer['category']] = $returned[$answer['category']] + $points;

        }
        return $returned;
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
            $users = $field->user()->get();
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

    /**
     * Get the score of the user of a specific assessment
     *
     * @param $user_code
     * @return mixed
     */
    public function getScore($user_code)
    {
        $user_db = DB::table('users')->where('code', $user_code)->first();
        $assessmen_id = Input::get('assessment_id');
        $user = User::find($user_db->id);
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['scores'] = $user->getScoresOfAQuestionnare($assessmen_id);
        return $data;
    }

//    public function getAnswers($user_code)
//    {
//        $user_db = DB::table('users')->where('code', $user_code)->first();
//        $assessmen_id = Input::get('assessment_id');
//        // dd($assessmen_id);
//        $user = User::find($user_db->id);
//        $user_response = $user->getAnswersOfAQuestionnare($assessmen_id)->get();
//        $data = array();
//        foreach ($user_response as $response) {
//            $questionnare = Questionnaire::find($response['questionnaire_id']);
//            $data['questionnaire'] = $questionnare;
//           // $response['questionnaire'] = $questionnare;
//            foreach ($response['questionnaire']->questions()->get() as $question) {
//                $question = $assessment->questions()->get();
//                $assessment['question'] = $question;
//
//            }
//        }
//        $data['statues'] = "200 Ok";
//        $data['error'] = null;
//        $data['data']['assessment'] = $user->getAnswersOfAQuestionnare($assessmen_id)->get();
//        return $data;
//    }
}
