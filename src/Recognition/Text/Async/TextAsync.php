<?php

namespace Tuputech\Recognition\Text\Async;

use Tuputech\Exception\TuputechSDKException;

class TextAsync
{
    private $content;
    private $callbackUrl;
    private $contentId;
    private $userId;
    private $forumId;

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
        // 去除空值
        foreach ($keys as $key => $value) {
            if (!is_null($key) && !is_null($value)) {
                $effectiveArr[$key] = $value;
            }
        }
        return json_encode($effectiveArr);
   }

   public function apiEncode()
   {
       return $this->__toString();
   }

   public function clear()
   {
       $this->content = null;
       $this->callbackUrl = null;
       $this->callbackRule = null;
       $this->contentId = null;
       $this->userId = null;
       $this->forumId = null;
   }
}

 function is_not_null($val)
 {
     return !is_null($val);
 }