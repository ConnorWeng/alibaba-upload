<?php

require_once 'Util.class.php';
require_once 'Config.class.php';
require_once 'OpenAPI.class.php';

session_start();

$numIid = $_REQUEST['state'];
$categoryId = $_REQUEST['categoryId'];

$taobaoItem = OpenAPI::getTaobaoItem($numIid);
$price = $taobaoItem->price;
$detail = addslashes($taobaoItem->desc);
$title = $taobaoItem->title;
$num = $taobaoItem->num;
$picUrl = '["'.$taobaoItem->pic_url.'"]';
$freightType = 'F';

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="http://lib.sinaapp.com/js/jquery/1.7.2/jquery.min.js"></script>           
<script src="ckeditor/ckeditor.js"></script>
</head>
<body>
<center>
<div style="width:800px;">
<form name="mainform" action="offer_new.php" method="POST">
    标题：<input style="width:400px" type="text" name="title" value="<?php echo($title); ?>"></input><br/>
    产品属性：<br/>
         <?php
            $features = OpenAPI::offerPostFeatures($categoryId)->result->toReturn;
            foreach ($features as $feature) {
                if (count($feature->featureIdValues) > 0) {
                    echo($feature->name);
                    echo('<select name="%%%%%%'.$feature->fid.'">');
                    foreach ($feature->featureIdValues as $value) {
                        echo('<option value="'.$value->value.'">'.$value->value.'</option>');
                    }
                    echo('</select><br/>');
                }
            }
         ?>
    价格：<input type="text" name="price" value="<?php echo($price); ?>"></input><br/>
    数量：<input type="text" name="amount" value="<?php echo($num); ?>"></input><br/>
    详情：<textarea name="detail" value="<?php echo($taobaoItem->desc); ?>"></textarea><br/>
    运输：<input type="text" name="freightType" value="<?php echo($freightType); ?>"></input><br/>
         <input type="hidden" name="categoryId" value="<?php echo($categoryId); ?>"/>
    <input type="submit" value="一键上传宝贝"/>
</form>
</div>
<center>
<script>
var editor = CKEDITOR.replace('detail');
</script>           
</body>
</html>
