<?php

class TupuClient
{
    private $secretId;
    private $apiUrl;
    private $privateKey;

    const TupuApi = 'http://api.open.tuputech.com/v3/recognition/';
    //'http://api4.open.tuputech.com/v3/recognition/'

    const ErrUnsorted = -1;
    const ErrWrongInput = -2;
    const ErrUnrecognizedResult = -3;
    const ErrWrongSignature = -4;
    const ErrEmptyPrivateKey = -5;

    public function __construct($privateKey, $secretId)
    {
        $this->privateKey = $privateKey;
        $this->secretId = $secretId;
        $this->apiUrl = self::TupuApi . $secretId;
    }

    public function recognition($images)
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
        $sign_string = $this->secretId . "," . $timestamp . "," . $nonce;

        $pkey = openssl_pkey_get_private( $this->privateKey );
        openssl_sign($sign_string, $signature, $pkey, OPENSSL_ALGO_SHA256);
        $signature = base64_encode($signature);

        $data = array(
            'image' => $images,
            'timestamp' => $timestamp,
            'nonce' => $nonce,
            'signature' => $signature
        );

        return $this->request($data);
    }

    private function request($data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
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

            $pkey = openssl_get_publickey( $this->_getTupuPublicKey() );
            $verifyRes = openssl_verify($json, base64_decode($signature), $pkey, "sha256WithRSAEncryption");
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