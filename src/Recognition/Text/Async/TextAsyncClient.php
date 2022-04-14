<?php

namespace Tuputech\Recognition\Text\Async;

use Tuputech\Common\Core;

class TextAsyncClient extends Core
{

    private $textAsync;
    const APIURL = 'http://api.text.tuputech.com/v3/recognition/text/async/';

    public function __construct($private)
    {
        parent::__construct($private);
        $this->setAPIUrl(self::APIURL);
        $this->textAsync = new TextAsync();
    }

    public static function initGlobalTextAsyncClient($private)
    {
        $GLOBALS['TextAsyncClient'] = new TextAsyncClient($private);
        return $GLOBALS['TextAsyncClient'];
    }
    

    /**
     * @Description 
     * @param [string] secretID tupu应用ID
     * @param [string] content 审核的文本
     * @param [string] callbackUrl 回调Url
     * @param [array optFunc] optFuncs 配置函数
     */
    public function perform($secretId, $content, $callbackUrl, ...$optFuncs)
    {
        if (!is_string($secretId) || !is_string($content) || !is_string($callbackUrl) || !is_array($optFuncs))
        {
            throw new TuputechSDKException("ParamError", "input params error, please check your params.");
        }
        $this->textAsync->content = $content;
        $this->textAsync->callbackUrl = $callbackUrl;
        foreach ($optFuncs as $optFunc)
        {
            $optFunc($this->textAsync);
        }
        $recording = $this->textAsync->apiEncode();
        $this->textAsync->clear();
        $data = substr($recording, 1, strlen($recording)-2);

        return $this->recognitionWithJSON($secretId, $data);
    }
}

/**
 * @Description (optional)
 * @param [string] $roomId
 * @return void
 */
function withContentId($contentId)
{
    if (!is_string($contentId))
    {
        throw new TuputechSDKException("ParamError", "input params error, please check your params.");
    }
    
    return function ($textAsync) use ($contentId) {
        $textAsync->contentId = $contentId;
    };
}


/**
 * @Description (Optional)
 * @param [string] $userId
 * @return void
 */
function withUserId($userId)
{
    if (!is_string($userId))
    {
        throw new TuputechSDKException("ParamError", "input params error, please check your params.");
    }
    
    return function ($textAsync) use ($userId) {
        $textAsync->userId = $userId;
    };
}

/**
 * @Description (Optional)
 * @param [string] $forumId
 * @return void
 */
function withForumId($forumId)
{
    if (!is_string($forumId))
    {
        throw new TuputechSDKException("ParamError", "input params error, please check your params.");
    }

    return function ($textAsync) use ($forumId) {
        $textAsync->forumId = $forumId;
    };
}