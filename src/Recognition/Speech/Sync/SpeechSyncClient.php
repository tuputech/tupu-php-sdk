<?php

namespace Tuputech\Recognition\Speech\Sync;

use Tuputech\Common\Core;
use Tuputech\Common\DataInfo;
use Tuputech\Exception\TuputechSDKException;

class SpeechSyncClient extends Core {

    const APIURL = 'http://api.speech.tuputech.com/v3/recognition/speech/';

    public static function initGlobalSpeechClient($private_key_path)
    {
        $GLOBALS['GTupuSpeechClient'] = new SpeechSyncClient($private_key_path);
        return $GLOBALS['GTupuSpeechClient'];
    }

    public function __construct($private_key_path)
    {
        parent::__construct($private_key_path);
        $this->setAPIUrl(self::APIURL);
    }

    /**
     * performWithBinaryArr
     * api 识别接口，使用二进制数据
     * @param  string $secret_id  在图普开通的应用ID
     * @param  array  $binarys 识别数据的二进制数组: key 作为 fileName，value 作为 审核数据
     * @param  array  $tasks 指定本次调用审核的任务id，可选
     * @return
     */
    public function performWithBinarys($secret_id, $binarys, ...$tasks)
    {
        if (!is_string($secret_id) || !is_array($data_info_arr))
        {
            echo '[perform with binary ERROR]: input illage';
            throw new TuputechSDKException("ParamError", "input params error, please check your params.");
        }

        $data_info_arr = array();
        foreach ($binarys as $key => $value)
        {
            $dtif = new DataInfo();
            $dtif->setBuf($value, $key);
            $data_info_arr[] = $dtif;
        }

        return $this->recognition($secret_id, $data_info_arr, $tasks);
    }

    /**
     * performWithLocalPath
     * @param string $secret_id
     * @param array<strig> $paths
     * @param function ...$optFunc
     * @return void
     */
    public function performWithLocalPath($secret_id, $paths, ...$tasks)
    {
        if (!is_string($secret_id) || !is_array($paths))
        {
            echo '[perform with binary ERROR]: input illage';
            throw new TuputechSDKException("ParamError", "input params error, please check your params.");
        }

        $data_info_arr = array();
        foreach ($paths as $path)
        {
            $dtif = new DataInfo();
            $dtif->setLocalPath($path);
            $data_info_arr[] = $dtif;
        }

        return $this->recognition($secret_id, $data_info_arr, $tasks);
    }

    /**
     * performWithUrls
     *
     * @param [type] $secret_id
     * @param [type] $paths
     * @param [type] ...$optFunc
     * @return void
     */
    public function performWithUrls($secret_id, $urls, ...$tasks)
    {
        if (!is_string($secret_id) || !is_array($urls))
        {
            throw new TuputechSDKException("ParamError", "input params error, please check your params.");
        }

        $data_info_arr = array();
        foreach ($urls as $url)
        {
            $dtif = new DataInfo();
            $dtif->setRemoteUrl($url);
            $data_info_arr[] = $dtif;
        }

        return $this->recognition($secret_id, $data_info_arr, $tasks);
    }
}