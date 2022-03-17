<?php
require 'vendor/autoload.php';

// use Tuputech\Common\Core;
// use Tuputech\Hello;

// $core = new \Tuputech\Hello();
// $core->say();
$log = new Monolog\Logger('name');
$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::WARNING));

$log->addWarning('Foo');
?>
