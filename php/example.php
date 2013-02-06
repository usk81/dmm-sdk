<?php

require_once 'src/dmm.php';

$dmm = new Dmm(array(
	'appId'       => 'xxxxxxxxxxxxx',
	'affiliateId' => 'xxxxx-999',
));

$result = $dmm->api();

var_dump($result);