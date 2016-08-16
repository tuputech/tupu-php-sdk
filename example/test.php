#!/usr/bin/php
<?php

require '../tupuclient.php';

function main() {
	$images = array('@img/1.jpg', '@img/2.jpg');

	//NOTE: Paste the path of your private key pem file here
	$privateKey = file_get_contents('...');

	//NOTE: Paste your Screct-ID here
	$secretId = '...';

	$tupu = new TupuClient($privateKey, $secretId);

	$result = $tupu->recognition($images);
	var_dump($result);
}

main();
