<?php

namespace Tuputech\Common;

class Verifier
{

    private $public_key;

    public function __construct()
    {
        $this->public_key = $this->_getTupuPublicKey();
    }

    public function verify($message, $signature)
    {

        $_verifyRes = openssl_verify($message, base64_decode($signature), $this->public_key, "sha256WithRSAEncryption");
        return $_verifyRes;
    }

    private function _getTupuPublicKey()
    {
        //Publish key of Tupu API
        $_public_key = <<<EOF
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDyZneSY2eGnhKrArxaT6zswVH9
/EKz+CLD+38kJigWj5UaRB6dDUK9BR6YIv0M9vVQZED2650tVhS3BeX04vEFhThn
NrJguVPidufFpEh3AgdYDzOQxi06AN+CGzOXPaigTurBxZDIbdU+zmtr6a8bIBBj
WQ4v2JR/BA6gVHV5TwIDAQAB
-----END PUBLIC KEY-----
EOF;
        return $_public_key;
    }
}