<?php

namespace App\Http\Controllers\API;

use App\Answer;
use App\Field;
use App\Grade;
use App\Question;
use App\Questionnaire;
use App\User;
use App\Value;
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
        $this->middleware('auth')->only('show', 'getAssessmentsNames', 'getAssessment', 'storeAssessment', 'storeValuesAssessment', 'storeValuesAssessmentSorted', 'storeMultiAssessment', 'storeTextAssessment', 'storeKteerAssessment');
        $this->middleware('expert')->only('getScore', 'getAnswers', 'getUserAssessment');
        $this->middleware('admin')->only('removeUserAssessment');
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
        $assessments = DB::table('questionnaires')->where('language', Input::get('language'))->select('name', 'id', 'language', 'description', 'type')->get();
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
        // dd($assessment->type);
        switch ($assessment->type) {
            case "mcq":
                $questions = $assessment->questions()->get();
                $data['statues'] = "200 Ok";
                $data['error'] = null;
                foreach ($questions as $question) {
                    $answers = $question->answers()->get();
                    $question['answers'] = $answers;
                }
                $data['data']['questions'] = $questions;
                return $data;
                break;
            case "values":
                $questions = $assessment->questions()->get();
                $data['statues'] = "200 Ok";
                $data['error'] = null;
                $data['data']['questions'] = $questions;
                return $data;
                break;
            case "multi":
                $questions = $assessment->questions()->get();
                $data['statues'] = "200 Ok";
                $data['error'] = null;
                $data['data']['questions'] = $questions;
                return $data;
                break;
            case "text":
                $questions = $assessment->questions()->get();
                $data['statues'] = "200 Ok";
                $data['error'] = null;
                $data['data']['questions'] = $questions;
                return $data;
                break;
            case "kteer":
                $questions = $assessment->questions()->get()[0];
                $data['statues'] = "200 Ok";
                $data['error'] = null;

                $answers = $questions->answers()->get();
                $questions['answers'] = $answers;

                $data['data']['questions'] = $questions;
                return $data;
                break;
            default:
                return null;
        }

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
        $questionnare_out = new Questionnaire();
        foreach ($data as $category) {
            if ($category != "")
                $returned[$category['category']] = 0;
        }
        // dd($returned);
        $i = 0;
        foreach ($data as $answer) {
            if ($i >= 1) {
                $question = Question::findOrFail($answer['question_id']);
                $answer_db = Answer::findOrFail($answer['answer_id']);
                $questionnare = Questionnaire::findOrFail($answer['questionnaire_id']);
                // dd($questionnare);
                // $answer_db['user_id']= $user->id;
                //$user->answers()->save($answer_db);
                $questionnare->answers()->save($answer_db, ["user_id" => $user->id]);
                $questionnare_out = $questionnare;
                $points = $answer['points'];
                $grade = new Grade(['user_id' => $user->id, 'answer_id' => $answer_db->id, 'questionnaire_id' => $questionnare->id, 'score' => $points, 'category' => $answer['category']]);
                $grade->save();
                $returned[$answer['category']] = $returned[$answer['category']] + $points;
            }
            $i++;
        }
        $questionnare_out->user()->save($user);

        return $returned;
    }

    public function storeValuesAssessment(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();
        //dd($request->all());
        $returned = [];
        $questionnare_out = new Questionnaire();
        // dd($returned);
        $user_assessments = $user->questioners()->get();
        //dd($data);
        $questionnare = Questionnaire::findOrFail($data[0]['questionnaire_id']);
        foreach ($user_assessments as $assessment) {
            if ($assessment->id == $questionnare->id) {
                $data1['statues'] = "302 Ok";
                $data1['error'] = "This assessment hs been taken before";
                $data1['data'] = null;
                return response()->json($data1, 302);
            }
        }

        foreach ($data as $answer) {

            $question = Question::findOrFail($answer['question_id']);
            $answer_db = new Answer(['question_id' => $question->id, 'points' => $answer['points'], 'answer_content' => 'values assessment']);
            $answer_db->save();

            $question->answers()->save($answer_db);
            // dd($questionnare);
            // $answer_db['user_id']= $user->id;
            //$user->answers()->save($answer_db);
            $questionnare->answers()->attach($answer_db, ["user_id" => $user->id, 'answer_id' => $answer_db->id]);
            $questionnare_out = $questionnare;
            $points = $answer['points'];
            $grade = new Grade(['user_id' => $user->id, 'answer_id' => $answer_db->id, 'questionnaire_id' => $questionnare->id, 'score' => $points, 'category' => ""]);
            $grade->save();
            array_push($returned, $grade);
            //$returned[$answer['category']] = $returned[$answer['category']] + $points;
        }
        $questionnare_out->user()->save($user);

        return $user->getScoresOfValuesQuestionnare($questionnare_out->id);
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
        $questionnaire = Questionnaire::find($assessmen_id);
        switch ($questionnaire->type) {
            case "mcq":
                $user = User::find($user_db->id);
                $data['statues'] = "200 Ok";
                $data['error'] = null;
                $data['data']['scores'] = $user->getScoresOfAQuestionnare($assessmen_id);
                return $data;
                break;
            case "values":
                $user = User::find($user_db->id);
                $data['statues'] = "200 Ok";
                $data['error'] = null;
                $data['data']['scores'] = $user->getScoresOfValuesQuestionnareSorted($assessmen_id);
                return $data;
                break;
            case "multi":
                $user = User::find($user_db->id);
                $data['statues'] = "200 Ok";
                $data['error'] = null;
                $data['data']['scores'] = $user->getScoresOfMultiQuestionnare($assessmen_id);
                return $data;
                break;
            case "kteer":
                $user = User::find($user_db->id);
                $data['statues'] = "200 Ok";
                $data['error'] = null;
                $data['data']['scores'] = $user->getScoresOfKteerQuestionnare($assessmen_id);
                return $data;
                break;
            case "text":
                $user = User::find($user_db->id);
                $data['statues'] = "200 Ok";
                $data['error'] = null;
                $data['data']['scores'] = $user->getScoresOfTextQuestionnare($assessmen_id);
                return $data;
                break;

            default:
                $data['statues'] = "200 Ok";
                $data['error'] = null;
                $data['data']['scores'] = null;
                break;
        }

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

    public function getUserAssessment($user_code)
    {
        $user_db = DB::table('users')->where('code', $user_code)->first();
        $user = User::find($user_db->id);
        $user_assessments = $user->questioners()->get();
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data']['assessments'] = $user_assessments;
        return $data;
    }

    /**
     * Remove the assessment submission from a user
     *
     * @param $user_code
     * @return \Illuminate\Http\JsonResponse
     */

    public function removeUserAssessment($user_code)
    {
        $user_db = DB::table('users')->where('code', $user_code)->first();
        $user = User::find($user_db->id);
        $assessment = Questionnaire::find(Input::get('assessment_id'));
        $user->questioners()->detach($assessment);
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data'] = null;
        return response()->json($data, 200);
    }

    /**
     * Store users sorted values
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeValuesAssessmentSorted(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();
        foreach ($data as $key => $value) {
            //dd($data);
            $value_db = new Value(['answer_content' => $value['answer_content'], 'points' => $value['points'], 'user_id' => $user->id, 'question_id' => $value['question_id'], 'questionnaire_id' => $value['questionnaire_id'], 'answer_id' => $value['id'], 'rank' => ($key + 1)]);
            $value_db->save();
        }
        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data'] = null;
        return response()->json($data1, 200);
    }

    public function getSortedValues($user_code)
    {
        $user_db = DB::table('users')->where('code', $user_code)->first();
        $user = User::find($user_db->id);

    }

    /**
     * Store multi valued assessment
     *
     * @param Request $request
     * @return array
     */

    public function storeMultiAssessment(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();
        $questionnaire_out = Questionnaire::find($data[0]['questionnaire_id']);
        $questionnaire_out->user()->attach($user);

        foreach ($data as $grade) {
            $question_id = $grade['question_id'];
            $questionnaire_id = $grade['questionnaire_id'];
            $questionnare = Questionnaire::find($questionnaire_id);
            $answers = $grade['Answers'];
            foreach ($answers as $index => $answer) {
                if ($answer != -1) {
                    $question = Question::findOrFail($question_id);
                    $answer_db = new Answer(['question_id' => $question_id, 'points' => $index, 'answer_content' => 'multi assessment']);
                    $answer_db->save();

                    $question->answers()->save($answer_db);
                    $questionnare->answers()->attach($answer_db, ["user_id" => $user->id, 'answer_id' => $answer_db->id]);
                    $grade = new Grade(['user_id' => $user->id, 'answer_id' => $answer_db->id, 'questionnaire_id' => $questionnare->id, 'score' => $index, 'category' => "multi"]);
                    $grade->save();
                }
            }
        }
        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data'] = null;
        return response()->json($data1, 200);
    }

    /**
     * Store text assessment
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeTextAssessment(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();
        $questionnaire_out = Questionnaire::find($data[0]['questionnaire_id']);
        $questionnaire_out->user()->attach($user);
        foreach ($data as $grade) {
            if ($grade['enable'] == true) {
                $question_id = $grade['question_id'];
                $question = Question::findOrFail($question_id);
                $answer_db = new Answer(['question_id' => $question_id, 'points' => -1, 'answer_content' => $grade['answer']]);
                $answer_db->save();
                $question->answers()->save($answer_db);
                $questionnaire_out->answers()->attach($answer_db, ["user_id" => $user->id, 'answer_id' => $answer_db->id]);
                $grade = new Grade(['user_id' => $user->id, 'answer_id' => $answer_db->id, 'questionnaire_id' => $questionnaire_out->id, 'score' => -1, 'category' => "text"]);
                $grade->save();
            }

        }
        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data'] = null;
        return response()->json($data1, 200);
    }

    /**
     * Store kteer assessment
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function storeKteerAssessment(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();
        $questionnaire = Questionnaire::findOrFail($data['questionnaire_id']);
        $questionnaire->user()->attach($user);
        $question_id = $data['question_id'];
        foreach ($data['Answers'] as $index => $answer) {
            if ($answer != -1) {
                $question = Question::findOrFail($question_id);
                $answer_db = Answer::findOrFail($answer);
                $question->answers()->save($answer_db);
                $questionnaire->answers()->attach($answer_db, ["user_id" => $user->id, 'answer_id' => $answer_db->id]);
                $grade = new Grade(['user_id' => $user->id, 'answer_id' => $answer, 'questionnaire_id' => $questionnaire->id, 'score' => $index, 'category' => "kteer"]);
                $grade->save();
            }
        }
        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data'] = null;
        return response()->json($data1, 200);
    }

    public function getClients()
    {
        $users = User::all();
        $returned = [];
        foreach ($users as $user) {
            if ($user->isA('client') && $user->isNotA('expert', 'admin', 'hadmin', 'ladmin')) {
                array_push($returned,$user);
            }
        }
        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data']['clients'] = $returned;
        return response()->json($data1, 200);
    }

}
