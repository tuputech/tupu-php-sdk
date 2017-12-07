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
$images = array('@img/1.jpg', '@img/2.jpg');


//Providing tag(s) relative to images
$tags = array('Bob', 'Bob');


//Setting camera ID(s) relative to images
$CIDs = array('tupu_test');


//NOTE: Paste the path of your private key pem file here
$privateKey = file_get_contents('./your_private_key.pem');

//NOTE: Paste your Screct-ID here
$secretId = 'your_secret_id';

$tupu = TupuClient::initBiGlobalInstance($privateKey);

$result = $tupu->biRecognition($secretId, $images, $tags, $CIDs);
var_dump($result);
