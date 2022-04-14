<?php

namespace TestPhp;

require_once '/path/to/vendor/autoload.php';

/*
*/
use Tuputech\Recognition\Text\Async\TextAsyncClient;
use Tuputech\Exception\TupuSDKException;
// 使用配置函数
use function Tuputech\Recognition\Text\Async\withForumId;
use function Tuputech\Recognition\Text\Async\withRoomId;

try
{
    // STEP1. 设置账号私钥路径
    // $priveKeyPath = "your rsa private key path";
    $priveKeyPath = "rsa_private_key.pem";

    // STEP2. 创建 api 接口的 cli 实例
    $textCli = new TextAsyncClient($priveKeyPath);

    // 账号对应的任务 SecretID
    // $secretId = "your secretID";
    // $callbackUrl = "your recive callback result url";
    $secretId = "your secretID";
    $callbackUrl = "http://172.26.2.63:19611";
    // 需要审核的文本内容
    $content = "content test";

    // STEP3. 执行识别. 参数: 任务 secretID, 识别文本内容, 接受回调结果的 url; 可选参数, 可参考 api 文档描述. 提供 withSpeechAsyncCallbackRule, withRoomId, withUserId, withForumId 函数
    $result = $textCli->perform($secretId, $content, $callbackUrl, withForumId("testForumID"), withContentId("testRoomID"), withForumId("testForumID"));
    // $result = $textCli->performWith($secretId, $content, $callbackUrl, withForumId("testForumID"));

    echo $result;
}
catch(TupuSDKException $e) {
    echo $e;
}