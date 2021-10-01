<?php

namespace App\Http\Middleware;

use App\Helpers\AuthHelper;
use App\Helpers\Helper;
use App\Models\User;
use Closure;
use Firebase\JWT\ExpiredException;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $token = $request->bearerToken();

        $authHelper = new AuthHelper();
        try {
            $decoded = $authHelper->decode($token);

            if (empty($decoded)) {
                return Helper::respondWithError(401, 'Unauthorized');
            }

            $user = User::where('id', $decoded->data->id ?? null)->first();
            if (empty($user)) {
                return Helper::respondWithError(401, 'Unauthorized');
            }

            $request->_user = $user;
            return $next($request);
        } catch (ExpiredException $e) {
            return Helper::respondWithError(401, 'Unauthorized');
        }
        return $next($request);
    }
}
