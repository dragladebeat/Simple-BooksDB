<?php

namespace App\Helpers;

use App\Models\BlacklistedToken;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use stdClass;

class AuthHelper
{
    private $secret = null;
    private $algorithm = null;

    function __construct()
    {
        $this->secret = env('JWT_SECRET');
        $this->algorithm = env('JWT_ALGORITHM', 'HS256');
    }
    public function encode($data)
    {
        $iat = CarbonImmutable::now();
        $exp = $iat->addHour();

        $payload['iat'] = $iat->timestamp;
        $payload['exp'] = $exp->timestamp;
        $payload['data'] = $data;
        $payload['jti'] = sha1($iat . json_encode($data));

        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    public function decode($token)
    {
        $decoded = JWT::decode($token, $this->secret, [$this->algorithm]);

        $blacklisted = BlacklistedToken::where('token', $decoded->jti)->first();
        if (!empty($blacklisted)) {
            throw new ExpiredException('Unauthorized');
        }

        return $decoded;
    }

    public function login($email, $password)
    {
        $user = User::where('email', $email)->first();

        if (empty($user)) {
            return null;
        }

        if (!Hash::check($password, $user->password)) {
            return null;
        }

        $data = new stdClass();
        $data->id = $user->id;

        $token = $this->encode($data);

        return $token;
    }

    public function logout($token)
    {
        $decoded = $this->decode($token);
        $blacklistedToken = new BlacklistedToken();
        $blacklistedToken->token = $decoded->jti;
        $blacklistedToken->save();
    }

    public function refresh($token)
    {
        $decoded = $this->decode($token);

        $newToken = $this->encode($decoded->data);

        $this->logout($token);

        return $newToken;
    }

    public function getUser($token)
    {
        $decoded = $this->decode($token);

        if (empty($decoded)) {
            return null;
        }

        $user = User::where('id', $decoded->data->id)->first();

        return $user;
    }
}
