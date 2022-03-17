<?php

namespace Tuputech\Common;

use Tuputech\Exception\TupuSDKException;

class Core
{
    const RootAPIURL = 'http://api.open.tuputech.com/v3/recognition/';
    const DefaultTimeout = 30;
    const DefaultUserAgent = 'tupu-php-client/1.0';

    private $signer;
    private $verifier;
    private $apiUrl;
    private $UID;
    private $timeout;
    private $user_agent;

    public function __construct($private_key)
    {
        $this->signer = new Signature($private_key);
        $this->verifier = new Verifier();
        // set default value
        $this->_initDefault();
    }

    /**
     * setUserAgent
     *
     * @param string $agent
     * @return void
     */
    public function setUserAgent($agent)
    {
        $this->user_agent = $agent;
        return $this;
    }

    public function setAPIUrl($url)
    {
        $this->apiUrl = $url;
        return $this;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function setUID($Uid)
    {
        $this->UID = $Uid;
        return $this;
    }

    public function recognition($secret_id, $data_info_arr, $tasks)
    {
        // step1. illage input checkout
        if (!is_array($data_info_arr) || count($data_info_arr) <= 0 || !is_string($secret_id) || !is_array($tasks))
        {
            throw new TupuSDKException("ParamError", "input params error, please check your params.");
        }
        
        // step2. get "timestamp, nonce, signature" params
        $params = $this->_getAuthParams($secret_id);
        $boundary = $this->_getBoundary();
        $body = array();

        if (is_string($this->UID))
        {
            $params['uid'] = $this->UID;
        }

        // file & optional request body params
        foreach ($data_info_arr as $data_info)
        {
            $body = array_merge($body,  $this->_addDataInfoField($data_info, $boundary));
        }

        // appoint task request body params
        foreach ($tasks as $task)
        {
            $body = array_merge($body, $this->_constructFormData($boundary, "task", $task));
        }

        // auth request body params
        foreach ($params as $_key => $_value)
        {
            $body = array_merge($body, $this->_constructFormData($boundary, $_key, $_value));
        }

        $body[] = '--' . $boundary . '--';
        $body[] = '';
        $contentType = 'multipart/form-data; boundary=' . $boundary;
        $content = join("\r\n", $body);
        
        return $this->_request($secret_id, $content, $contentType);
    }

    public function recognitionWithJSON($secret_id, $data)
    {
        if (!is_string($secret_id) || !is_string($data))
        {
            throw new TupuSDKException("ParamError", "input params error, please check your params.");
        }
        
        $params = $this->_getAuthParams($secret_id);
        $params_json_str = json_encode($params);

        $body = sprintf("{ %s, %s }", $data, substr($params_json_str, 1, strlen($params_json_str)-2));
        return $this->_request($secret_id, $body, JsonConentType);
    }

    private function _request($secret_id, $data, $contentType)
    {
        if (!is_string($secret_id) || !is_array($data) || !is_string($contentType))
        {
            throw new TupuSDKException("ParamError", `input params error, please check your params. $secret_id, $data, $contentType`);
        }

        $curl = curl_init();
        // setting curl opt params
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl . $secret_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => array(
                'Content-Length: ' . strlen($contentType),
                'Expect: 100-continue',
                'Content-Type: ' . $contentType,
                'User-Agent:' . $this->user_agent,
            ),
            CURLOPT_POSTFIELDS => $data
        ));

        $result = curl_exec($curl);
        $errno = curl_errno($curl);
        if ($errno) {
            // retry one twice
            $result = curl_exec($curl);
            $errno = curl_errno($curl);
            if ($errno) {
                return $errno;
            }
        }
        curl_close($curl);

        return $this->_verifyResult($result);
    }

    private function _verifyResult($result)
    {
        if (is_int($result))
        {
            // curl request api error
            throw new TupuSDKException("ParamError", "input params error, please check your params.");
        }

        $data = json_decode($result);
        if ($data) {
            $signature = $data['signature'];
            $json = $data['json'];

            if ($this->verifier->verify($json, $signature) == 1) {
                //verfied
                return json_decode($json, true);
            } else {
                throw new TupuSDKException("SignError", "Signature Error, please check your private or secretId");
                // return ErrWrongSignature;
            }
        }
        throw new TupuSDKException("RecogationError", "recognition data is null");
    }

    private function _addDataInfoField($data_info, $boundary)
    {
        if (!is_object($data_info))
        {
            throw new TupuSDKException("ParamError", "input params error, please check your params.");
        }

        $body = array();

        $_remote_url = $data_info->getRemoteUrl();
        $_file_type = $data_info->getFileType();
        $_local_path = $data_info->getLocalPath();
        $_buf = $data_info->getBuf();
        $_optional_params = $data_info->getOtherMessage();

        // construct form data for recognition file or url \ binary
        if (strlen($_remote_url) > 0)
        {
            $body = array_merge($body, $this->_constructFormData($boundary, $_file_type, $_remote_url));
        }
        else if (strlen($_local_path) > 0)
        {
            // uploda local file
            $body[] = '--' . $boundary;
            $body[] = 'Content-Disposition: form-data; name="' . $_file_type . '"; filename="' . basename($_local_path) . '"';
            $body[] = 'Content-Type: application/octet-stream';
            $body[] = '';
            $body[] = file_get_contents($_local_path);
        }
        else if (strlen($_buf) > 0)
        {
            //  uploda binary data
            $body[] = '--' . $boundary;
            $body[] = 'Content-Disposition: form-data; name="' . $_file_type . '"; filename="' . basename($_local_path) . '"';
            $body[] = 'Content-Type: application/octet-stream';
            $body[] = '';
            $body[] = $_buf;
        }

        // construct form data for reognition api other optional params
        foreach ($_optional_params as $_key => $_value)
        {
            $body = array_merge($body, $this->_constructFormData($boundary, $_key, $_value));
        }
        return $body;
    }

    private function _constructFormData($boundary, $form_data_name, $form_data_value)
    {
        $form = array();
        $form[] = '--' . $boundary;
        $form[] = 'Content-Disposition: form-data; name="' . $form_data_name . '"';
        $form[] = '';
        $form[] = $form_data_value;
        return $form;
    }

    private function _getAuthParams($secretId)
    {
        $timestamp = time();
        $nonce = rand(100, 999999);
        $signature = $this->signer->sign($secretId, $timestamp, $nonce);
        $authParams = array(
            'timestamp' => $timestamp,
            'nonce' => $nonce,
            'signature' => $signature,
        );
        return $authParams;
    }

    private function _getBoundary()
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

        return $boundary;
    }

    private function _initDefault()
    {
        $this->apiUrl = Core::RootAPIURL;
        $this->timeout = Core::DefaultTimeout;
        $this->user_agent = Core::DefaultUserAgent;
    }
}