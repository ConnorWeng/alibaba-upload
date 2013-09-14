<?php

require_once 'OpenAPI.class.php';

session_start();

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<?php

$categoryId = $_REQUEST['categoryId'];
$price = $_REQUEST['price'];
if (get_magic_quotes_gpc() == 0) {
    $detail = addslashes(addslashes($_REQUEST['detail']));
} else {
    $detail = addslashes($_REQUEST['detail']);
}
$title = $_REQUEST['title'];
$freightType = $_REQUEST['freightType'];
$mixWholeSale = check($_REQUEST['mixWholeSale']);

/* upload image */
$picUrl = $_REQUEST['picUrl'];
$albumId = $_REQUEST['albumId'];
$localImgFile = '@'.OpenAPI::downloadImage($picUrl);
$uploadResult = OpenAPI::ibankImageUpload($albumId, uniqid(), $localImgFile)->result->toReturn[0];
unlink(substr($localImgFile,1));
$imageUriList = '["http://img.china.alibaba.com/'.$uploadResult->url.'"]';

$productFeatures = '{';
foreach ($_REQUEST as $key=>$val) {
    if (strstr($key, '%%%%%%')) {
        $productFeatures = $productFeatures.'"'.substr($key, 6).'":"'.$val.'",';
    }
}
$productFeatures = substr($productFeatures, 0, strlen($productFeatures) - 1).'}';

$offer = '{"bizType":"1","categoryID":"'.$categoryId.'","supportOnlineTrade":"true","pictureAuthOffer":"false","priceAuthOffer":"false","skuTradeSupport":"true","mixWholeSale":"'.$mixWholeSale.'","priceRanges":"1:'.$price.'","amountOnSale":"100","offerDetail":"'.$detail.'","subject":"'.$title.'","imageUriList":'.$imageUriList.',"freightType":"'.$freightType.'","productFeatures":'.$productFeatures.'}';

$result = OpenAPI::offerNew(stripslashes($offer));

if ($result->result->success) {
    echo('上传成功');
} else {
    echo($result->message[0]);
}

function check($v) {
    if ($v == 'on') {
        return 'true';
    } else {
        return 'false';
    }
}

?>
</body>
</html>