<?php

require_once 'Util.class.php';
require_once 'Config.class.php';

session_start();

$url = 'https://gw.open.1688.com/openapi/http/1/system.oauth2/getToken/1006478?grant_type=authorization_code&need_refresh_token=true&client_id=1006478&client_secret=C7OMfhfK3C!T&redirect_uri='.urlencode(Config::get('redirect_uri')).'&code=' . $_REQUEST['code'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POSTFIELDS, '');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$data = curl_exec($ch);
curl_close($ch);
$result = json_decode($data);

$current = time() * 1000;

$_SESSION['access_token'] = $result->access_token;
$_SESSION['refresh_token'] = $result->refresh_token;

$title = $_REQUEST['state'];

header('Location: offer_new.php?offer=%7b%22bizType%22%3a%221%22%2c%22categoryID%22%3a%221037264%22%2c%22supportOnlineTrade%22%3atrue%2c%22pictureAuthOffer%22%3afalse%2c%22priceAuthOffer%22%3afalse%2c%22mixWholeSale%22%3atrue%2c%22subject%22%3a%22'.$title.'%22%2c%22freightType%22%3a%22F%22%2c%22sendGoodsAddressId%22%3a%22840222%22%2c%22freightTemplateId%22%3a%221%22%2c%22amountOnSale%22%3a1000%2c%22priceRanges%22%3a%22100%3a21.33%601000%3a14.15%602000%3a13.48%22%2c%22offerWeight%22%3a0.05%7d');

?>
