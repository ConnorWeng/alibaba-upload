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
$num = $taobaoItem->num;
$picUrl = '["'.$taobaoItem->pic_url.'"]';
$freightType = 'F';

$offer = '{"bizType":"1","categoryID":"'.$categoryId.'","supportOnlineTrade":"true","pictureAuthOffer":"false","priceAuthOffer":"false","skuTradeSupport":"true","mixWholeSale":"false","priceRanges":"1:'.$price.'","amountOnSale":"100","offerDetail":"'.$detail.'","subject":"'.$title.'","imageUriList":'.$picUrl.',"freightType":"'.$freightType.'"}';

$searchResult = OpenAPI::categorySearch($title)->result;
$catIDs = '';
if ($searchResult->total > 0) {
    foreach ($searchResult->toReturn as $val) {
        $catIDs = $catIDs.$val.',';
    }
}
if (stripos($catIDs, ',')) {
    $catIDs = substr($catIDs, 0, strlen($catIDs) - 1);
}
$catList = OpenAPI::getPostCatList($catIDs)->result->toReturn;

//header('Location: '.'offer_new.php?offer='.urlencode($offer));

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
input {
  width: 400px;
}
</style>
<script src="ckeditor/ckeditor.js"></script>
</head>
<body>
<center>
<div style="width:800px;">
<form name="mainform" action="offer_new.php" method="POST">
    标题：<input type="text" name="title" value="<?php echo($title); ?>"></input><br/>
    分类：<select name="categoryId">
        <?php
        foreach ($catList as $cat) {
            echo('<option value="'.$cat->catsId.'">'.$cat->catsName.'</option>');
        }
        ?>
        </select><br/>
    价格：<input type="text" name="price" value="<?php echo($price); ?>"></input><br/>
    数量：<input type="text" name="amount" value="<?php echo($num); ?>"></input><br/>
    详情：<textarea name="detail" value="<?php echo($detail); ?>"></textarea><br/>
    运输：<input type="text" name="freightType" value="<?php echo($freightType); ?>"></input><br/>
    <input type="submit" value="一键上传宝贝"/>
</form>
</div>
<center>
<script>
var editor = CKEDITOR.replace('detail');
editor
</script>           
</body>
</html>
