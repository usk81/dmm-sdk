<?php

require_once 'dmm.php';

$dmm = new Dmm(array(
	'appId'       => 'xxxxxxxxxxxxx',
	'affiliateId' => 'xxxxx-999',
));

$result = $dmm->api();

echo $result;
}