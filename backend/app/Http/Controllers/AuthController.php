<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\Helper;
use App\Models\Author;
use App\Models\Encryption;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthController extends Controller
{

    private $authHelper;
    public function __construct(AuthHelper $authHelper)
    {
        $this->authHelper = $authHelper;
    }
    public function login(Request $request)
    {
        Log::info(json_encode($request->all()));
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|string',
                'password' => 'required|string',
            ]
        );

        if ($validator->fails()) {
            throw new BadRequestHttpException($validator->errors()->first());
        }


        $token = $this->authHelper->login($request->email, $request->password);

        if (empty($token)) {
            return Helper::respondWithError(401, 'Unauthorized');
        }

        return $this->respondWithToken($token);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->authHelper->logout($request->bearerToken());

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:5',
                // // must contain at least one lowercase letter
                // 'regex:/[a-z]/',
                // // must contain at least one uppercase letter
                // 'regex:/[A-Z]/',
                // // must contain at least one digit
                // 'regex:/[0-9]/',
                // // must contain a special character
                // 'regex:/[@$!%*#?&]/',
            ]
        ]);

        if ($validator->fails()) {
            if ($validator->fails()) {
                Log::error($validator->errors()->first());
                throw new BadRequestHttpException($validator->errors()->first());
            }
        }

        // Check if user already exist
        if (User::where('email', $request->input('email'))->exists()) {
            return Helper::respondWithError(406, 'User already exist');
        }

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));

        if ($user->save()) {
            return $this->login($request);
        }

        return Helper::respondWithError(500, 'Unknown error occured');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        try {
            $token = $this->authHelper->refresh($request->bearerToken());
            return $this->respondWithToken($token);
        } catch (ExpiredException $e) {
            return Helper::respondWithError('401', $e->getMessage());
        }
    }


    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $decoded = $this->authHelper->decode($token);
        $user = $this->authHelper->getUser($token);
        return response()->json([
            'user' => $user,
            'auth' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_at' => new Carbon($decoded->exp),
            ]
        ]);
    }
}
