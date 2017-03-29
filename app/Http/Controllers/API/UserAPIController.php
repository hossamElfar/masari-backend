<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\User;
/**
 * Class UserAPIController
 * @package App\Http\Controllers\API
 */

class UserAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => [
        ]]);

    }


      /**
       * getUser is used to get all user's profile data
       */

    public function getUser($userID){
        // get specified user by id
        $user = User::find($userID);

        // check if the user is valid
        if(!$user)
          // create an error json object to be send in the http response
          return response()->json([
              'status'=> '404',
              'message'=> 'Bad Request',
              "errors"=>
                  [
                    'resourse' => 'users',
                    'message'=> 'Invalid User ID'
                  ]

            ]
            ,404);


          // the user is valid

          // get most recently five answers answered by this user
          $answers =  $user->lastFiveAnswers();

          // get most recently five questions asked by this user
          $questions = $user->lastFiveQuestions();

          // create returned success json object
          return response()->json(
            [
            'status'=> '200',
            'message'=> 'OK',
            'results'=>[
                'first_name'      => $user->first_name,
                'last_name'       => $user->last_name,
                'email'           => $user->email,
                'major'           => $user->major,
                'semester'        => $user->semester,
                'bio'             => $user->bio,
                'profile_picture' => $user->profile_picture,
                'questions'       => $questions,
                'answers'         => $answers
            ]
          ]
             ,200);

    }

}
