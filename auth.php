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

var_dump($_SESSION['access_token']);
var_dump($_SESSION['refresh_token']);

$taobaoItem = OpenAPI::getTaobaoItem($_REQUEST['state']);

$title = $taobaoItem->title;
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


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="http://lib.sinaapp.com/js/jquery/1.7.2/jquery.min.js"></script>           
<style>
input {

}
</style>
<script src="ckeditor/ckeditor.js"></script>
</head>
<body>
<center>
<div style="width:800px;">
<form name="mainform" action="edit.php" method="POST">
    <input type="hidden" name="state" value="<?php echo($_REQUEST['state']); ?>"/>
    分类：<select name="categoryId">
         <?php
         foreach ($catList as $cat) {
             echo('<option value="'.$cat->catsId.'">'.$cat->catsName.'</option>');
         }
         ?>
         </select><br/>
         <input type="submit" value="下一步"></input>
</form>
</div>
<center>
<script>
var editor = CKEDITOR.replace('detail');
</script>           
</body>
</html>
