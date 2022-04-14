<?php

namespace TestPhp;

require_once '/path/to/vendor/autoload.php';

/*
*/
use Tuputech\Recognition\Text\Sync\TextSyncClient;
use Tuputech\Exception\TupuSDKException;

try
{
    // STEP1. 设置账号私钥路径
    $priveKeyPath = "your rsa private key path";
    print_r($priveKeyPath, "\n");

    // STEP2. 创建 api 接口的 cli 实例
    $textCli = new TextSyncClient($priveKeyPath);

    // 账号对应的任务 SecretID
    $secretId = "your secretID";

    $text = [
        [
            // 必须
            "content" => "test text content",
            // 可选
            "contentId" => "test_content_id",
            "userId" => "test_user_id",
            "forumId" => "test_forum_id",
        ],
    ];

    // STEP3. 执行识别. 参数: 任务 secretID, 文本数组：参考 api 文档
    $result = $textCli->perform($secretId, $text);

    echo $result;
}
catch(TupuSDKException $e) {
    echo $e;
}