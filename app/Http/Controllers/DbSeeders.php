<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Question;
use App\Questionnaire;
use Illuminate\Http\Request;

class DbSeeders extends Controller
{
    /**
     * Seed DB with arabic values assessment
     *
     * @param Request $request
     * @return mixed
     */
    public function seedValuesArabic(Request $request)
    {
        $questionnaire = Questionnaire::create(["name"=>"feelings","no_of_questions"=>4,"language"=>"en"]);
        $questionnaire->type="feelings";
        $questionnaire->save();
        $data_raw = $request->all();
        $data = $data_raw['data'];
        foreach ($data as $question){
            $question1 = new Question([
                'question_content' => $question,
                'category' => 'Discover your feelings',
                'no_of_answers' => 4,
                'questionnaire_id' => $questionnaire->id
            ]);
            $question1->save();
        }

        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data'] = null;
        return $data1;
    }

    public function seedSkillsEnglish(Request $request)
    {
        $questionnaire = Questionnaire::create(["name"=>"Skills","no_of_questions"=>4,"language"=>"en"]);
        $questionnaire->type="skills";
        $questionnaire->save();
        $data_raw = $request->all();
        $data = $data_raw['data'];
        foreach ($data as $question) {
            try{
                $question1 = new Question([
                    'question_content' => $question['content'],
                    'category' => $question['category'],
                    'no_of_answers' => 4,
                    'questionnaire_id' => $questionnaire->id,
                    'title'=>'some title'
                ]);
                $question1->save();
            }catch (\Exception $e){
                dd($question);
            }

        }
        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data'] = null;
        return $data1;
    }

    public function seedDecisionArabic(Request $request)
    {
        $questionnaire = Questionnaire::create(["name"=>"feelings","no_of_questions"=>4,"language"=>"en"]);
        $questionnaire->type="decision";
        $questionnaire->save();
        $data = $request->all();
        $question1 = new Question([
            'question_content' => $data['data'][0],
            'category' => 'feelings',
            'no_of_answers' => 4,
            'questionnaire_id' => $questionnaire->id
        ]);
        $question1->save();
      // dd($data['data']);
        foreach ($data['data'] as $key=> $answer) {
            if ($key >=1){
                $answer_db = new Answer([
                    'answer_content' => $answer,
                    'question_id' => $question1->id,
                    'points' => -1
                ]);
                $answer_db->save();
            }
        }
        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data'] = null;
        return $data1;
    }

    public function seedDecisionArabicAnswers(Request $request)
    {
        $data = $request->all();
        $question = Question::find($data['question_id']);

    }
}
