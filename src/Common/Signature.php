<?php

namespace Tuputech\Common;

class Signature
{

    private $private_key;

    public function __construct($private_key_path)
    {
        $this->private_key = file_get_contents($private_key_path);
    }

    public function sign($secretId, $timestamp, $nonce) {
        $sign_string = $secretId . "," . $timestamp . "," . $nonce;
        openssl_sign($sign_string, $_signature, $this->private_key, OPENSSL_ALGO_SHA256);
        $_signature = base64_encode($_signature);
        return  $_signature;
    }
}
