<?php

namespace TestPhp;

require_once '/path/to/vendor/autoload.php';

use Tuputech\Recognition\Image\ImageClient;
use Tuputech\Exception\TupuSDKException;

try {
    // STEP1. 私钥路径
    $priveKeyPath = __DIR__."/rsa_private_key.pem";

    // STEP2. 创建 API 接口实例
    $imgCli = new ImageClient($priveKeyPath);

    // STEP3. 应用 secretID
    $secretId = "5e8ef0139d76e4379aca105f";
    // STEP4. 图片链接数组(支持一次请求审核多张图片)

    //      方式一. 图片链接
    $url = ["https://baike.baidu.com/pic/%E7%BA%A6%E7%91%9F%E5%A4%AB%C2%B7%E6%8B%9C%E7%99%BB/5363618/1/21a4462309f7905298224b3ee7bac0ca7bcb0a467fd3?fr=lemma&ct=single"];

    //      方式二. 本地图片路径数组(支持一次请求审核多张图片)
    $localFilePath = ["/home/huangchongzheng/tmp/enmap.jpeg"];

    //      方式三. 内存中的图片二进制数据(支持一次请求审核多张图片)
    $binary = array("baideng.wav" => file_get_contents($localFilePath[0]));

    // STEP5. 请求对应的数据类型识别方法
    $binaryResult = $imgCli->performWithBinarys($secretId, $binary);
    // 审核本地音频文件
    $fileResult = $imgCli->performWithLocalPath($secretId, $localFilePath);
    // 审核内存中的二进制音频文件
    $binaryResult = $imgCli->performWithBinarys($secretId, $binary);

    echo $result;
    echo $fileResult;
    echo $binaryResult;
}
catch(TupuSDKException $e) {
    echo $e;
}