<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Bouncer;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Support\Facades\Auth;

use Tymon\JWTAuth\Token;
use JWTAuth;

class Expert
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $authenticated = true;
        $token = $request->header('x-access-token');
        if ($token) {
            try {
                $var = JWTAuth::decode(new Token($token));
                $user = User::findOrFail($var['id']);
                if (Bouncer::is($user)->an('expert','admin','admin_level_1','admin_level_2')) {
                    Auth::setUser($user);
                } else {
                    $authenticated = false;
                }

            } catch (TokenInvalidException $e) {
                $authenticated = false;
                if ($request->ajax() || $request->wantsJson())
                    return response()->json(['error' => 'Unauthorized.'], 401);
                return redirect()->guest('login');
            }
        } else if (Auth::guard($guard)->guest())
            $authenticated = false;

        if ($authenticated)
            return $next($request);
        if ($request->ajax() || $request->wantsJson())
            return response()->json(['error' => 'Unauthorized.'], 401);
        return response()->json(['error' => 'Unauthorized.'], 401);
    }
}
