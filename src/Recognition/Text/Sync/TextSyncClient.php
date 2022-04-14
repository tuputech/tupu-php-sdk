<?php

namespace Tuputech\Recognition\Text\Sync;

use Tuputech\Common\Core;

class TextSyncClient extends Core
{

    const APIURL = 'http://api.text.tuputech.com/v3/recognition/text/';

    public function __construct($private)
    {
        parent::__construct($private);
        $this->setAPIUrl(self::APIURL);
    }

    public static function initGlobalTextSyncClient($private)
    {
        $GLOBALS['TextSyncClient'] = new TextSyncClient($private);
        return $GLOBALS['TextSyncClient'];
    }
    
    public function perform($secretId, $textArr)
    {
        if (!is_string($secretId) || !is_array($textArr))
        {
            throw new TuputechSDKException("ParamError", "input params error, please check your params.");
        }
        $data = '"text":'.json_encode($textArr);
        return $this->recognitionWithJSON($secretId, $data);
    }
}
