<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use Closure;
use Exception;
use Illuminate\Support\Facades\Log;

class Encryption
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
        $secret = md5($request->bearerToken());

        if (!empty($request->all())) {
            if (!$request->has('payload')) {
                throw new Exception('Data not encrypted', 400);
            }
            $encrypted = $request->input('payload');
            $decrypted = $this->decrypt($encrypted, $secret, 'aes-256-gcm', 16);

            if (empty($decrypted)) {
                throw new Exception('Failed to decrypt', 400);
            }

            $request->replace((json_decode($decrypted, true)));
        }
        // Log::info("Decrypted Request : " . json_encode($request->all()));

        $response = $next($request);
        // Log::info($response->getContent());
        if (!empty($response->getContent())) {
            $payload = $this->encrypt($response->getContent(), $secret, 'aes-256-gcm', 16);
            $response->setContent(json_encode(['payload' => $payload]));
        }
        // Log::info($secret);
        // Log::info($request->bearerToken());
        return $response;
    }

    private function encrypt($data, $key, $method, $tag_length)
    {
        $iv_len = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($iv_len);
        $tag = ""; // will be filled by openssl_encrypt

        $ciphertext = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv, $tag, "", $tag_length);
        return $encrypted = base64_encode($iv . $ciphertext . $tag);
    }

    private function decrypt($encrypted, $key, $method, $tag_length)
    {
        $data = base64_decode($encrypted);
        $iv_len = openssl_cipher_iv_length($method);
        $iv = substr($data, 0, $iv_len);
        $cipher_text = substr($data, $iv_len, -$tag_length);
        $tag = substr($data, -$tag_length);

        return $decrypted = openssl_decrypt($cipher_text, $method, $key, OPENSSL_RAW_DATA, $iv, $tag);
    }
}
