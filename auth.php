<?php
require_once 'Util.class.php';
session_start();
?>
<html>
<head>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
<style>

</style>
</head>
<body>
<center>
<div>code: <?php echo($_REQUEST['code']); ?></div>
<?php
$url = 'https://gw.open.1688.com/openapi/http/1/system.oauth2/getToken/1006478?grant_type=authorization_code&need_refresh_token=true&client_id=1006478&client_secret=C7OMfhfK3C!T&redirect_uri=http%3a%2f%2flocalhost%2fphp%2fauth.php&code=' . $_REQUEST['code'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POSTFIELDS, '');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$data = curl_exec($ch);
curl_close($ch);
$result = json_decode($data);
echo ' refresh token: '.$result->refresh_token;
echo ' access token: '.$result->access_token;

$current = time() * 1000;

$_SESSION['access_token'] = $result->access_token;
$_SESSION['refresh_token'] = $result->refresh_token;

?>
<form method="POST" action="http://gw.open.1688.com/openapi/param2/1/cn.alibaba.open/offer.new/1006478?offer=%7b%22bizType%22%3a%221%22%2c%22categoryID%22%3a%221037264%22%2c%22supportOnlineTrade%22%3atrue%2c%22pictureAuthOffer%22%3afalse%2c%22priceAuthOffer%22%3afalse%2c%22mixWholeSale%22%3atrue%2c%22subject%22%3a%22LM358DRDDD%22%2c%22freightType%22%3a%22F%22%2c%22sendGoodsAddressId%22%3a%22840222%22%2c%22freightTemplateId%22%3a%221%22%2c%22amountOnSale%22%3a1000%2c%22priceRanges%22%3a%22100%3a21.33%601000%3a14.15%602000%3a13.48%22%2c%22offerWeight%22%3a0.05%7d&_aop_timestamp=<?php echo($current) ?>&access_token=<?php echo($result->access_token); ?>&_aop_signature=<?php

    $url = 'http://gw.open.1688.com/openapi';
    $api = 'param2/1/cn.alibaba.open/offer.new';
    	
    //生成签名
    $code_arr = array(
        'offer' => '{"bizType":"1","categoryID":"1037264","supportOnlineTrade":true,"pictureAuthOffer":false,"priceAuthOffer":false,"mixWholeSale":true,"subject":"LM358DRDDD","freightType":"F","sendGoodsAddressId":"840222","freightTemplateId":"1","amountOnSale":1000,"priceRanges":"100:21.33`1000:14.15`2000:13.48","offerWeight":0.05}',
        '_aop_timestamp' => $current,
        'access_token' => $result->access_token
    );

    echo(Util::signDefault($url, $api, $code_arr));
                            ?>">
  <input type="submit" value="submit"/>
</form>
</center>
</body>
</html>
