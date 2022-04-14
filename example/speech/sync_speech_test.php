<?php

namespace TestPhp;

require_once '/path/to/vendor/autoload.php';

/*
*/
use Tuputech\Recognition\Speech\Sync\SpeechSyncClient;
use Tuputech\Exception\TupuSDKException;

try
{
    $priveKeyPath = "your rsa private key path";
    print_r($priveKeyPath, "\n");

    // 
    $spCli = new SpeechSyncClient($priveKeyPath);

    $secretId = "your secretID";
    $url = ["your speech file url. e.g: http://www.test.com/test.wav"];
    $localFilePath = ["your local file path. e.g: /home/test/Downloads/test.wav"];
    $binary = array(
        "your speech file name. e.g. terror.wav " => "your speech binary data"
    );
    // follow this 
    // $binary = array("terror.wav" => file_get_contents($localFilePath[0]));

    // 审核 url 音频文件
    $result = $spCli->performWithUrls($secretId, $url);

    // 审核本地音频文件
    $fileResult = $spCli->performWithLocalPath($secretId, $localFilePath);

    // 审核内存中的二进制音频文件
    $binaryResult = $spCli->performWithBinarys($secretId, $binary);

    echo $result;
    echo $fileResult;
    echo $binaryResult;
}
catch(TupuSDKException $e) {
    echo $e;
}