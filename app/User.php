<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Cmgmyr\Messenger\Traits\Messagable;

class User extends Authenticatable
{
    use HasRolesAndAbilities;
    use Notifiable;
    use Messagable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'email', 'password', 'second_name', 'phone', 'code', 'country', 'city', 'age', 'gender', 'pp',
        'pp', 'user_level', 'field_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function fileds()
    {
        return $this->belongsToMany('App\Field', 'fields_users');
    }

    public function grades()
    {
        return $this->hasMany('App\Grade');
    }

    public function links()
    {
        return $this->hasMany('App\Link');
    }

    public function messages()
    {
        return $this->hasMany('App\Message');
    }

    public function news()
    {
        return $this->hasMany('App\News');
    }

    public function pictures()
    {
        return $this->hasMany('App\Picture');
    }

    public function programs()
    {
        return $this->hasMany('App\Program');
    }

    public function qs()
    {
        return $this->hasMany('App\Q');
    }

    public function questioners()
    {
        return $this->belongsToMany('App\Questionnaire', 'questionnaires_users');
    }

    public function videos()
    {
        return $this->hasMany('App\Video');
    }

    public function events()
    {
        return $this->hasMany('App\Event');
    }

    public function timings()
    {
        return $this->hasMany('App\Timing');
    }

    public function request_client()
    {
        return $this->hasMany('App\Request', 'client_id');
    }

    public function request_expert()
    {
        return $this->hasMany('App\Request', 'expert_id');
    }

    public function answers()
    {
        return $this->hasMany('App\Answer');
    }

    public function getAnswersOfAQuestionnare($questionnare_id)
    {
        return $this->grades()->where('questionnaire_id', $questionnare_id);
    }

    public function getAnswersOfAQuestionnareOfACategory($questionnare_id, $category)
    {
        return $this->grades()->where(['questionnaire_id' => $questionnare_id, 'category' => $category]);
    }

    public function getScoresOfAQuestionnare($questionnare_id)
    {
        $grades = $this->grades()->where('questionnaire_id', $questionnare_id)->get();
        $returned = [];
        foreach ($grades as $grade) {
            $returned[$grade->category] = 0;
        }
        foreach ($grades as $grade) {
            $returned[$grade->category] = $returned[$grade->category] + $grade->score;
        }
        return $returned;
    }

    public function isAssessmentTake(Questionnaire $q)
    {
        $submitted = $this->questioners()->get();
        $flag = false;
        foreach ($submitted as $ass) {
            if ($q->id == $ass->id) {
                $flag = true;
            }
        }
        return $flag;
    }

    public function getScoresOfValuesQuestionnare($id)
    {
        $assessment = Questionnaire::find($id);
        $returned = [];
        $grades = $this->grades()->where('questionnaire_id', $id)->get();
        //return $grades[0]->answer()->get();
        // return $answers;
        for ($n = 7; $n >= 1; $n--) {

            foreach ($grades as $grade) {
                //   dd($grade->answer()->get()[0]->points);
                if ($grade->answer()->get()[0]->points == $n) {
                    $grade->answer()->get()[0]['questionnaire_id'] = $id;
                    $temp = $grade->answer()->get()[0];
                    $temp['questionnaire_id'] = $id;

                    array_push($returned, $temp);
                }
            }
            if (sizeof($returned) >= 10) {
                return $returned;
            }

        }
    }

    public function getScoresOfValuesQuestionnareSorted($id)
    {
        $assessment = Questionnaire::find($id);
        // dd($assessment->values()->get());
        $values = $assessment->values()->where('user_id', $this->id)->get()->sortBy('rank')->unique();
        foreach ($values as $value) {
            $value['question'] = $value->question()->get()[0];
        }
        return $values;
    }

    public function getScoresOfMultiQuestionnare($id)
    {
        $questionnaire = Questionnaire::find($id);
        $question_ids = [];
        $returned = [];
        $demo = array();
        $values = $questionnaire->grades()->where('user_id', $this->id)->get();
        foreach ($values as $value) {
            $question = $value->answer()->get()[0]->question()->get()[0];
            if (array_key_exists(''.$question->id, $demo)){
                array_push($demo[$question->id],$question->answers()->where('id',$value['answer_id'])->get());
            }else{
                $demo[''.$question->id] = array();
                array_push($demo[$question->id],$question->answers()->where('id',$value['answer_id'])->get());
            }

        }
        foreach ($demo as $key => $ret){
            $question = Question::find($key);
            $question['answers'] = $ret;
            array_push($returned,$question);
        }
        return $returned;
    }

    public function getScoresOfKteerQuestionnare($id)
    {
        $questionnaire = Questionnaire::find($id);
        $values = $questionnaire->grades()->where('user_id', $this->id)->get();
        foreach ($values as $value) {
            $value['answer_content'] = $value->answer()->get()[0];
            $value['question'] = $value->answer()->get()[0]->question()->get()[0];
        }
        return $values;
    }

    public function getScoresOfTextQuestionnare($id)
    {
        $questionnaire = Questionnaire::find($id);
        $values = $questionnaire->grades()->where('user_id', $this->id)->get();
        foreach ($values as $value) {
            $value['answer_content'] = $value->answer()->get()[0];
            $value['question'] = $value->answer()->get()[0]->question()->get()[0];
        }
        return $values;
    }


    public function values()
    {
        return $this->hasMany('App\Value');
    }
}
