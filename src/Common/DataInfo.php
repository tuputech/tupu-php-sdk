<?php

namespace Tuputech\Common;

class DataInfo
{
    private $buff;
    private $file_type;
    private $remote_url;
    private $local_path;
    private $file_name;
    // map
    private $other_message;

    public function clear()
    {
        $this->buff = NULL;
        $this->file_type = NULL;
        $this->remote_url = NULL;
        $this->local_path = NULL;
        $this->file_name = NULL;
        $this->other_message = NULL;
    }

    public function setBuf($buffer, $file_name)
    {
        $this->buff = $buffer;
        $this->file_name = $file_name;
    }

    public function getBuf()
    {
        return $this->buff;
    }

    public function setFileType($file_type)
    {
        $this->file_type = $file_type;
    }

    public function getFileType()
    {
        return $this->file_type;
    }

    public function setRemoteUrl($remote_url)
    {
        $this->remote_url = $remote_url;
    }

    public function getRemoteUrl()
    {
        return $this->remote_url;
    }

    public function setLocalPath($local_path)
    {
        $this->local_path = $local_path;
    }

    public function getFileName()
    {
        return $this->file_name;
    }

    public function getLocalPath()
    {
        return $this->local_path;
    }

    public function setOtherMessage($key, $value)
    {
        $this->other_message[$key] = $value;
    }

    public function getOtherMessage()
    {
        return $this->other_message;
    }
}