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
echo($result->refresh_token);

$title = '';
if (!isset($_REQUEST['title'])) {
    $taobaoItem = OpenAPI::getTaobaoItem($_REQUEST['state']);
    $title = $taobaoItem->title;
} else {
    $title = $_REQUEST['title'];
}

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
    <input type="hidden" name="code" value="<?php echo($_REQUEST['code']); ?>"/>
    <input type="hidden" name="state" value="<?php echo($_REQUEST['state']); ?>"/>
    标题:<input style="width:400px;" type="text" name="title" value="<?php echo($title); ?>" />
    <input type="button" name="search" value="搜索分类" onclick="searchCategories()"/><br />
    分类:<select name="categoryId">
         <?php
         foreach ($catList as $cat) {
             echo('<option value="'.$cat->catsId.'">'.$cat->catsName.'</option>');
         }
         ?>
         </select><br/>
         <input type="button" value="下一步" onclick="next();"></input>
</form>
</div>
<center>
<script>
function searchCategories() {
    document.mainform.action = 'auth.php';
    document.mainform.submit();
}

function next() {
    if (document.mainform.categoryId.value == "") {
        alert('请选择合适的分类');
    } else {
        document.mainform.action = 'edit.php';
        document.mainform.submit();
    }
}
</script>           
</body>
</html>
