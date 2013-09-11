<?php

require_once 'Util.class.php';
require_once 'Config.class.php';
require_once 'OpenAPI.class.php';

session_start();

$isPostBack = isset($_REQUEST['title']);

if (!$isPostBack) {
    $url = 'https://gw.open.1688.com/openapi/http/1/system.oauth2/getToken/'.Config::get('app_id').'?grant_type=authorization_code&need_refresh_token=true&client_id='.Config::get('app_id').'&client_secret='.Config::get('secret_id').'&redirect_uri='.urlencode(Config::get('redirect_uri')).'&code=' . $_REQUEST['code'];

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
}

$title = '';
if (!$isPostBack) {
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
<script src="ckeditor/ckeditor.js"></script>
<link href="bootstrap/dist/css/bootstrap.css" rel="stylesheet">
<style>
body {
  padding-top: 40px;
  padding-bottom: 40px;
  background-color: #eee;
}

.form-signin {
  max-width: 600px;
  padding: 15px;
  margin: 0 auto;
}
.form-signin .form-signin-heading {
  margin-bottom: 10px;
}
.form-signin .checkbox {
  font-weight: normal;
}
.form-signin .form-control {
  position: relative;
  font-size: 16px;
  padding: 10px;
  -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
          box-sizing: border-box;
}
.form-signin .form-control:focus {
  z-index: 2;
}
.form-signin input[type="text"] {
  width: 400px;
  margin-bottom: -1px;
  border-bottom-left-radius: 0;
  border-bottom-right-radius: 0;
}
.form-label {
  width: 60px;
}
</style>           
</head>
<body>
<div class="container">
<form class="form-signin" name="mainform" action="edit.php" method="POST">
    <h3 class="form-signin-heading">如果搜索不到类目，请手动删除掉部分修饰词!</h3>
    <input type="hidden" name="code" value="<?php echo($_REQUEST['code']); ?>"/>
    <input type="hidden" name="state" value="<?php echo($_REQUEST['state']); ?>"/>         
    <table>
    <tr>
      <td class="form-label">标题:</td>
      <td><input class="form-control" type="text" name="title" value="<?php echo($title); ?>" /></td>
      <td><input class="btn btn-default" type="button" name="search" value="搜索分类" onclick="searchCategories()"/></td>
    </tr>
    <tr>
      <td class="form-label">分类:</td>
      <td><select name="categoryId">
         <?php
         foreach ($catList as $cat) {
             echo('<option value="'.$cat->catsId.'">'.$cat->catsName.'</option>');
         }
         ?>
      </select></td>
    <tr>
    </table>
    <input class="btn btn-lg btn-primary btn-block" type="button" value="下一步" onclick="next();"></input>
</form>
</div>
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
