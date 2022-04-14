<?php

namespace TestPhp;

require_once '/path/to/vendor/autoload.php';

/*
*/
use Tuputech\Recognition\Speech\Async\SpeechAsyncClient;
use Tuputech\Exception\TupuSDKException;
// 使用配置函数
use function Tuputech\Recognition\Speech\Async\withForumId;
use function Tuputech\Recognition\Speech\Async\withRoomId;

try
{
    // STEP1. 设置账号私钥路径
    $priveKeyPath = "your rsa private key path";

    // STEP2. 创建 api 接口的 cli 实例
    $speechCli = new SpeechAsyncClient($priveKeyPath);

    // 账号对应的任务 SecretID
    $secretId = "your secretID";
    // 需要识别的 URL
    $url = "https://r.tuputech.com/original/world/data-c40/yrw/api_test_data/vulgar.wmv";

    // STEP3. 执行识别. 参数: 任务 secretID, 识别链接 url, 可选参数，可参考 api 文档描述. 提供 withSpeechAsyncCallbackRule, withRoomId, withUserId, withForumId, withSpeechAsyncCallbackUrl 函数
    $result = $speechCli->performWithURL($secretId, $url, withForumId("testForumID"), withRoomId("testRoomID"));

    echo $result;
}
catch(TupuSDKException $e) {
    echo $e;
}