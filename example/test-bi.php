#!/usr/bin/php
<?php

/**
 * TUPU Recognition API SDK
 * Example for BI recognition service
 * Copyright(c)2013-2017, TUPU Technology
 * http://www.tuputech.com
 */


require '../tupuclient.php';

//Using remote iamge URLs
//$images = array('http://img.xxx.com/1.jpg', 'http://img.xxx.com/2.jpg');
//Upload files
$images = array('https://www.tuputech.com/original/world/data-node-n4/sd12/2018-01-05/10/5a0bb0d623d9ec9789c5b8b8/15151202099640.3988671594134976.jpg');


//Providing tag(s) relative to images
$tags = array('Bob', 'Bob');


//Setting camera ID relative to images
$CID = 'tupu_test';


//NOTE: Paste the path of your private key pem file here
$privateKey = file_get_contents('./my_private_key.pem');

//NOTE: Paste your Screct-ID here
$secretId = '58dbca8c677dba75c8213b00';

$tupu = TupuClient::initBiGlobalInstance($privateKey);

$async = false; //Using async callback to get result

$result = $tupu->biRecognition($secretId, $images, $tags, $CID, $async);
var_dump($result);
