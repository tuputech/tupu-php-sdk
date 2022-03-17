<?php

namespace Tuputech\Recognition\Speech\Async;

use Tuputech\Common\Core;

class SpeechAsyncClient extends Core
{

    private $speechAsync;
    const APIURL = 'http://api.speech.tuputech.com/v3/recognition/speech/async/';

    public function __construct($private)
    {
        parent::__construct($private);
        $this->setAPIUrl(self::APIURL);
        $speechAsync = new SpeechAsync();
    }

    public static function initGlobalClient($private)
    {
        $GLOBALS['SpeechAsyncClient'] = new SpeechAsyncClient($private);
        return $GLOBALS['SpeechAsyncClient'];
    }
    
    public function performWithURL($secretId, $url, ...$optFuncs)
    {
        if (!is_string($secretId) || !is_string($url) || !is_array($optFuncs))
        {
            throw new TuputechSDKException("ParamError", "input params error, please check your params.");
        }
        $this->speechAsync->url = $url;
        foreach ($optFuncs as $optFunc)
        {
            $optFunc($this->speechAsync);
        }
        $recording = $this->speechAsync->apiEncode();
        $this->speechAsync->clear();

        return $this->recognitionWithJSON($secretId, $recording);
    }
}

/**
 * @Description (optional)
 * @param [type] $rule
 * @return void
 */
function withSpeechAsyncCallbackRule($rule)
{
    if (!is_int($rule))
    {
        throw new TuputechSDKException("ParamError", "input params error, please check your params.");
    }

    return function ($speechAsync) use ($rule) {
        $speechAsync->callbackRule = $rule;
    };
}

/**
 * @Description (optional)
 * @param [string] $roomId
 * @return void
 */
function withRoomId($roomId)
{
    if (!is_string($roomId))
    {
        throw new TuputechSDKException("ParamError", "input params error, please check your params.");
    }
    
    return function ($speechAsync) use ($roomId) {
        $speechAsync->callbackRule = $roomId;
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
    
    return function ($speechAsync) use ($userId) {
        $speechAsync->userId = $userId;
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

    return function ($speechAsync) use ($forumId) {
        $speechAsync->forumId = $forumId;
    };
}