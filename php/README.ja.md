# DMM Unofficial PHP SDK (v0.1.0)

DMMは彼らが保有するデータベースを外部から参照するためのWeb APIを提供しています。

このリポジトリは、あなたのPHPアプリからDMMのWebAPIにアクセスするためのオープンソースのPHP SDKが含まれています。

参照
-----

[DMM Web API 公式リファレンス](https://affiliate.dmm.com/api/)

使い方
-----
使用するために最小限必要なこと:

	require 'dmm-sdk/php/src/dmm.php'

    $dmm = new Dmm(array(
      'appId'  => 'YOUR_APP_ID',
      'affiliateId' => 'YOUR_AFFILIATE_ID',
    ));

APIの呼び方:

	$result = $dmm->api();
	var_dump($result);

テスト
-----

Sorry!! Don't make yet.
