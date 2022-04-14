# 简介
欢迎使用 Tupu PHP SDK，SDK是 API 审核接口的配套工具。目前已经支持同步语音审核、异步点播语音审核、图片审核、文本审核等产品，后续所有的云服务产品都会接入进来。SDK 实现了帮助开发者加密生成和校验。
为方便 PHP 开发者调试和接入图普审核产品 API，这里向您介绍适用于 PHP 的 SDK，并提供首次使用开发工具包的简单示例。让您快速获取 TUPU PHP SDK 并开始调用。
# 依赖环境
1. PHP 5.6.0 版本及以上
2. 从 TUPU 控制台 开通相应产品
3. 获取 SecretID、账户私钥。

# 获取安装
安装 PHP SDK 前，先获取公私钥对。在第一次使用 API 之前，用户首先需要在 TUPU 控制台上申请审核任务和账户私钥，应用为 SecretID, 安全秘钥为 PrivateKey, SecretID 是用于标识 API 调用者的身份和调用服务，PrivateKey是用于加密签名字符串和服务器端验证签名字符串的密钥。PrivateKey 必须严格保管，避免泄露。
## 通过 Composer 安装
通过 Composer 获取安装是使用 PHP SDK 的推荐方法，Composer 是 PHP 的依赖管理工具，支持您项目所需的依赖项，并将其安装到项目中。关于 Composer 详细可参考 Composer 官网 。
1. 安装Composer：
    windows环境请访问[Composer官网](https://getcomposer.org/download/)下载安装包安装。
    
    unix环境在命令行中执行以下命令安装。
    > curl -sS https://getcomposer.org/installer | php

    > sudo mv composer.phar /usr/local/bin/composer
2. 建议中国大陆地区的用户设置腾讯云镜像源：`composer config -g repos.packagist composer https://mirrors.tencent.com/composer/`
3. 执行命令 `composer require tuputech/tupu-php-sdk` 添加依赖。
4. 在代码中添加以下引用代码。注意：如下仅为示例，composer 会在项目根目录下生成 vendor 目录，`/path/to/`为项目根目录的实际绝对路径，如果是在当前目录执行，可以省略绝对路径。
    
    > require '/path/to/vendor/autoload.php';

# 示例

还可以参考 SDK 仓库中 [examples](https://github.com/Tuputech/tupu-php-sdk/tree/master/examples) 目录中的示例，展示了更多的用法。

下面以图片审核接口 Image 为例:

### 简略版

```php
<?php
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
```

### 详细版

```php

```


# 常见问题

## 证书问题

## Web 访问异常

命令行下执行正常，但是放在 Web 服务器执行则报错：

cURL error 0: The cURL request was retried 3 times and did not succeed. The most likely reason for the failure is that cURL was unable to rewind the body of the request and subsequent retries resulted in the same error. Turn on the debug option to see what went wrong. See https://bugs.php.net/bug.php?id=47204 for more information. (see http://curl.haxx.se/libcurl/c/libcurl-errors.html)

此问题出现情况不一。可以运行 `php -r "echo sys_get_temp_dir();"` 打印系统默认临时目录绝对路径，然后在 `php.ini` 配置 `sys_temp_dir` 为这个值尝试是否能解决。

## 源码安装问题

为了支持部分源码安装的需要，我们将依赖的包文件放在 vendor 目录中，又考虑到不能造成对 composer 的不兼容，从 xxx 版本开始，我们暂时移除了源码安装，必须使用 composer 安装 SDK 和依赖的包。

## 关键字冲突问题

# 旧版SDK