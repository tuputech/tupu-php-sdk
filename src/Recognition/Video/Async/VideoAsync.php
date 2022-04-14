<?php

namespace Tuputech\Recognition\Video\Async;

use Tuputech\Exception\TuputechSDKException;

class VideoAsync
{
    private $url;
    private $callbackUrl;
    private $callbackRule;
    private $roomId;
    private $userId;
    private $forumId;
    private $customInfo;

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    // public function __construct($url, $callbackUrl)
    // {
    //     $this->url = $url;
    //     $this->callbackUrl = $callbackUrl;
    // }

    public function __toString()
    {
        $obj = clone $this;
        $keys = get_object_vars($obj);
        // $keys = array_filter($keys, 'is_not_null');
        $effectiveArr = array();
        foreach ($keys as $key => $value) {
            if (!is_null($key) && !is_null($value)) {
                $effectiveArr[$key] = $value;
            }
        }
        return json_encode(array( 'recording' => $effectiveArr));
   }

   public function apiEncode()
   {
       return $this->__toString();
   }

   public function clear()
   {
       $this->url = null;
       $this->callbackUrl = null;
       $this->callbackRule = null;
       $this->roomId = null;
       $this->userId = null;
       $this->forumId = null;
       $this->customInfo = null;
   }
}

 function is_not_null($val)
 {
     return !is_null($val);
 }