<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Mockery\CountValidator\Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

use Tymon\JWTAuth\Token;
use App\User;
use JWTAuth;
use JWTFactory;
use Response;
use Validator;
use Mail;
use Auth;
use Bouncer;


class AuthAPIController extends Controller
{
    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest', ['except' => 'logout']);
        $this->middleware('auth')->only('update');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|max:255',
            'second_name' => 'required|max:255',
            'phone' => 'required|max:20',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    protected function validatorUpdate(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'max:255',
            'second_name' => 'max:255',
            'phone' => 'max:20|unique:users',
            'email' => 'email|max:255|unique:users'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data)
    {
        $confirmation_code_init = str_random(5);
        $confirmation_code = strtolower($confirmation_code_init);
        Mail::send('auth.email.verify', ['confirmation_code' => $confirmation_code, 'name' => $data['first_name']], function ($message) use ($data) {
            $message->to($data['email'], $data['first_name'])
                ->subject('Verify your email address');
        });
        $user = new User();
        try {
            $user = User::create([
                'first_name' => $data['first_name'],
                'second_name' => $data['second_name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'code' => $confirmation_code,
                'user_level' => "0",
                'confirmed' => false
            ]);
        } catch (Exception $e) {
            $this->create($data);
        }
        return $user;
    }

    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails())
            return response()->json($validator->errors(), 302);
        $user = $this->create($request->all());
        $user->assign('client');
        return response()->json(['message' => '200 Ok', 'confirmation' => $user->code], 200);
    }

    public function verify($token)
    {
        $user = User::where('code', '=', $token)->first();
        if ($user) {
            $user->confirmed = true;
            $user->save();
            return response()->json(['message' => 'Ok'], 200);
        } else {
            return response()->json(['message' => 'invalid activation code'], 200);
        }
    }

    /**
     * Login for a User
     * @param  Request $request : must contain email and password of the User
     * @return json response containing an error in case of invalid credentials or
     * a server error or the token in case of valid credentials
     */
    public function login(Request $request)
    {
        // verify the credentials
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials, false, false))
            return response()->json(['error' => 'Invalid Credentials'], 401);

        //create token
        try {
            $user = User::where('email', '=', $credentials['email'])->first();
            $role = "";
            if (Bouncer::is($user)->an('client')) {
                $role = "client";
            }
            if (Bouncer::is($user)->an('expert')) {
                $role = "expert";
            }
            if (Bouncer::is($user)->an('ladmin')) {
                $role = "ladmin";
            }

            if (Bouncer::is($user)->an('hadmin')) {
                $role = "hadmin";
            }
            if (Bouncer::is($user)->an('admin')) {
                $role = "admin";
            }

            if ($user->confirmed == 0) {
                throw new JWTException;
            }
            $customClaims = [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $role
            ];
            $payload = JWTFactory::make($customClaims);
            $token = JWTAuth::encode($payload);
        } catch (JWTException $e) {
            // something went wrong
            dd($e);
            return response()->json(['error' => 'Could not create token'], 500);
        }

        // no errors, return the token
        return Response::json(['token' => $token->get(), "role" => $role]);
    }

    /**
     * Logout for a User
     */

    public function logout(Request $request)
    {
        try {
            if ($request->header('x-access-token'))
                JWTAuth::setToken(new Token($request->header('x-access-token')))->invalidate();
        } catch (TokenInvalidException $e) {
            return response()->json(['message' => 'invalid token'], 200);
        }
        return response()->json(['message' => 'Logged out.'], 200);
    }

    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $t = $request->intersect(['first_name', 'second_name', 'phone', 'country', 'city', 'age', 'gender', 'pp']);
        // dd($t);
        //$t = $request->all();
        $validator = $this->validatorUpdate($t);
        if ($validator->fails())
            return response()->json($validator->errors(), 400);
        $user->update($t);
        $user->save();
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data'] = $user;
        return response()->json($data, 200);
    }
}
