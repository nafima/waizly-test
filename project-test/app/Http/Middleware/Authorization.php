<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redis;

class Authorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function unauthorized() {
        return response()->json([
            'status' => 401,
            'message' => 'Unauthorized',
            'data' => [],
            'errors' => [
                'code' => 'UNAUTHORIZED',
                'detail' => null
            ],
        ], 401);
    }

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        if ($token) {
            $token = explode(' ', $token);
            $token = $token[1];
            if(!empty(Redis::keys('*:TOKEN:' . $token))) {
                $keys = Redis::keys('*:TOKEN:' . $token)[0];
                $process_keys = explode(':', $keys);
                $raw_user_id = explode("_", $process_keys[0]);
                $user_id = $raw_user_id[count($raw_user_id) - 1];

                $profile = Redis::get($user_id . ':TOKEN:' . $token);
                $request->attributes->set('profile', json_decode($profile, true));
                
                return $next($request);
            } else {
                return $this->unauthorized();
            }
        } else {
            return $this->unauthorized();
        }

        return $next($request);
    }
}
