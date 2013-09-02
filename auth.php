<?php

require_once 'Util.class.php';
require_once 'Config.class.php';
require_once 'OpenAPI.class.php';

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

$numIid = $_REQUEST['state'];

$taobaoItem = OpenAPI::getTaobaoItem($numIid);

$categoryId = '1037264';//$taobaoItem->cid;
$price = $taobaoItem->price;
$detail = addslashes($taobaoItem->desc);
$title = $taobaoItem->title;
$picUrl = '["'.$taobaoItem->pic_url.'"]';

$offer = '{"bizType":"1","categoryID":"'.$categoryId.'","supportOnlineTrade":"true","pictureAuthOffer":"false","priceAuthOffer":"false","skuTradeSupport":"true","mixWholeSale":"false","priceRanges":"1:'.$price.'","amountOnSale":"100","offerDetail":"'.$detail.'","subject":"'.$title.'","imageUriList":'.$picUrl.',"freightType":"F"}';

header('Location: '.'offer_new.php?offer='.urlencode($offer));

?>
