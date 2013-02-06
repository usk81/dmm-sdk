# DMM Unofficial PHP SDK (v0.1.0)

DMM provides Web APIs. For use from outside their database.

This repository contains the open source PHP SDK that allows you to access DMM Web API from your PHP app.

Refer
-----

[DMM Web API Official Refernce](https://affiliate.dmm.com/api/)

Usage
-----
The minimal you'll need to have is:

	require 'dmm-sdk/php/src/dmm.php'

    $dmm = new Dmm(array(
      'appId'  => 'YOUR_APP_ID',
      'affiliateId' => 'YOUR_AFFILIATE_ID',
    ));

To make API calls:

	$result = $dmm->api();
	var_dump($result);

Tests
-----

Sorry!! Don't make yet.