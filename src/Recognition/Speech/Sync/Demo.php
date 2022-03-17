<?php

// namespace Tuputech\Testing\Speech;

namespace Tuputech\Recognition\Speech\Sync;

use Tuputech\Test\Hello;
use Tuputech\Common\World;
use Tuputech\Common\Core;
use Tuputech\Recognition\Speech\Sync\SpeechSyncClient;

class Demo
{
    public function __construct()
    {
        // parent::__construct('./rsa_privkey.pem');
        $core = new Core("./private.pem");
        echo "hello world\n";
    }

    public function say()
    {
        $h = new Hello();
        $h->say();
        $w = new World("world");
        $w->say();
        echo "say hello\n\n";

        // $sp = new SpeechSyncClient();
        // $core = new Core("./test");
    }
}
