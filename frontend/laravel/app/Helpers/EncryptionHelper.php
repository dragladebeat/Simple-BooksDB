<?php

namespace App\Helpers;

use App\Helpers\Helper;
use Closure;
use Exception;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class EncryptionHelper
{

    private $key;
    private $method;
    private $tag_length;

    public function __construct($key)
    {
        $this->key = md5($key);
        $this->method = 'aes-256-gcm';
        $this->tag_length = 16;
    }

    public function encrypt($data)
    {
        $iv_len = openssl_cipher_iv_length($this->method);
        $iv = openssl_random_pseudo_bytes($iv_len);
        $tag = ""; // will be filled by openssl_encrypt

        $ciphertext = openssl_encrypt($data, $this->method, $this->key, OPENSSL_RAW_DATA, $iv, $tag, "", $this->tag_length);
        return $encrypted = base64_encode($iv . $ciphertext . $tag);
    }

    public function decrypt($data)
    {
        $data = base64_decode($data);
        $iv_len = openssl_cipher_iv_length($this->method);
        $iv = substr($data, 0, $iv_len);
        $cipher_text = substr($data, $iv_len, -$this->tag_length);
        $tag = substr($data, -$this->tag_length);

        return $decrypted = openssl_decrypt($cipher_text, $this->method, $this->key, OPENSSL_RAW_DATA, $iv, $tag);
    }
}
