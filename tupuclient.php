<?php

/**
 * TUPU Recognition API SDK (v1.1)
 * Copyright(c)2013-2016, TUPU Technology
 * http://www.tuputech.com
 */

class TupuClient
{
    private $apiUrl;
    private $publicKey;
    private $privateKey;
    private $uid;

    const TupuApi = 'http://api.open.tuputech.com/v3/recognition/';
    //'http://api4.open.tuputech.com/v3/recognition/'

    const ErrUnsorted = -1;
    const ErrWrongInput = -2;
    const ErrUnrecognizedResult = -3;
    const ErrWrongSignature = -4;
    const ErrEmptyPrivateKey = -5;

    public static function initGlobalInstance($privateKey, $apiUrl = self::TupuApi)
    {
        $GLOBALS["GTupuClient"] = new TupuClient($privateKey);
        return $GLOBALS["GTupuClient"];
    }
    public static function globalInstance()
    {
        return $GLOBALS["GTupuClient"];
    }


    public function __construct($privateKey, $apiUrl = self::TupuApi)
    {
        $this->publicKey = openssl_pkey_get_public( $this->_getTupuPublicKey() );
        $this->privateKey = openssl_pkey_get_private( $privateKey );
        $this->apiUrl = $apiUrl;
    }

    public function setUID($uid)
    {
        $this->uid = $uid;
    }

    public function recognition($secretId, $images, $tags)
    {
        if (!is_array($images)) {
            return self::ErrWrongInput;
        }
        if (!$this->privateKey) {
            return self::ErrEmptyPrivateKey;
        }

        $this->images = $images;
        $timestamp = time();
        $nonce = rand(100, 999999);
        $sign_string = $secretId . "," . $timestamp . "," . $nonce;

        openssl_sign($sign_string, $signature, $this->privateKey, OPENSSL_ALGO_SHA256);
        $signature = base64_encode($signature);

        $data = array(
            'timestamp' => $timestamp,
            'nonce' => $nonce,
            'signature' => $signature,
            'image' => $images
        );
        if (is_string($this->uid)) {
            $data['uid'] = $this->uid;
        }
        if (is_string($tags) || (is_array($tags) && count($tags) > 0)) {
            $data['tag'] = $tags;
        }

        return $this->request($secretId, $data);
    }

    private function request($secretId, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $secretId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $this->setPostfields($ch, $data);

        $result = curl_exec($ch);
        $errno = curl_errno($ch);
        if ($errno) {
            $result = curl_exec($ch);
            $errno = curl_errno($ch);
            if ($errno) {
                return $errno;
            }
        }
        curl_close($ch);
        
        $data = json_decode($result, true);
        if ($data) {
            $signature = $data['signature'];
            $json = $data['json'];

            $verifyRes = openssl_verify($json, base64_decode($signature), $this->publicKey, "sha256WithRSAEncryption");
            if ($verifyRes == 1) {
                //verfied
                return json_decode($json, true);
            } else {
                return self::ErrWrongSignature;
            }
        } else {
            return self::UnrecognizedResult;
        }
        return self::ErrUnsorted;
    }

    private function setPostfields($ch, $postfields, $headers = null)
    {
        $algos = hash_algos();
        $hashAlgo = null;
        foreach (array('sha1', 'md5') as $preferred) {
            if (in_array($preferred, $algos)) {
                $hashAlgo = $preferred;
                break;
            }
        }
        if ($hashAlgo === null) {
            list($hashAlgo) = $algos;
        }
        $boundary =
            '----------------------------' .
            substr(hash($hashAlgo, 'cURL-php-multiple-value-same-key-support' . microtime()), 0, 12);

        $body = array();
        $crlf = "\r\n";
        $fields = array();
        foreach ($postfields as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $fields[] = array($key, $v);
                }
            } else {
                $fields[] = array($key, $value);
            }
        }
        foreach ($fields as $field) {
            list($key, $value) = $field;
            if (strpos($value, '@') === 0) {
                preg_match('/^@(.*?)$/', $value, $matches);
                list($dummy, $filename) = $matches;
                $body[] = '--' . $boundary;
                $body[] = 'Content-Disposition: form-data; name="' . $key . '"; filename="' . basename($filename) . '"';
                $body[] = 'Content-Type: application/octet-stream';
                $body[] = '';
                $body[] = file_get_contents($filename);
            } else {
                $body[] = '--' . $boundary;
                $body[] = 'Content-Disposition: form-data; name="' . $key . '"';
                $body[] = '';
                $body[] = $value;
            }
        }
        $body[] = '--' . $boundary . '--';
        $body[] = '';
        $contentType = 'multipart/form-data; boundary=' . $boundary;
        $content = join($crlf, $body);
        $contentLength = strlen($content);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Length: ' . $contentLength,
            'Expect: 100-continue',
            'Content-Type: ' . $contentType,
        ));

        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);

    }

    private function _getTupuPublicKey()
    {
        //Publish key of Tupu API
        $publicKey = <<<EOF
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDyZneSY2eGnhKrArxaT6zswVH9
/EKz+CLD+38kJigWj5UaRB6dDUK9BR6YIv0M9vVQZED2650tVhS3BeX04vEFhThn
NrJguVPidufFpEh3AgdYDzOQxi06AN+CGzOXPaigTurBxZDIbdU+zmtr6a8bIBBj
WQ4v2JR/BA6gVHV5TwIDAQAB
-----END PUBLIC KEY-----
EOF;
        return $publicKey;
    }

}

/* End of file tupuclient.php */
