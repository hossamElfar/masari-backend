<?php

namespace App\Http\Controllers;

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
        $questionnaire = Questionnaire::find(1);
        $data = $request->all();
        $question = new Question([
            'question_content' => $data['content'],
            'category' => $data['category'],
            'no_of_answers' => 4,
            'questionnaire_id' => $questionnaire->id
        ]);
        $question->save();
        $data1['statues'] = "200 Ok";
        $data1['error'] = null;
        $data1['data'] = null;
        return $data1;
    }

    public function seedSkillsEnglish(Request $request)
    {
        $questionnaire = Questionnaire::find(2);
        $data = $request->all();
        foreach ($data as $question){
            $question1 = new Question([
                'question_content' => $question,
                'category' => 'multi',
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
}
