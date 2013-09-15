<?php

require_once 'Util.class.php';
require_once 'Config.class.php';
require_once 'OpenAPI.class.php';

session_start();

$numIid = $_REQUEST['state'];
$categoryId = $_REQUEST['categoryId'];

$taobaoItem = OpenAPI::getTaobaoItem($numIid);
$price = $taobaoItem->price;
$title = $taobaoItem->title;
$num = $taobaoItem->num;
$picUrl = $taobaoItem->pic_url;
$freightType = 'F';

$myAlbumList = OpenAPI::ibankAlbumList('MY')->result->toReturn;
$customAlbumList = OpenAPI::ibankAlbumList('CUSTOM')->result->toReturn;
$features = OpenAPI::offerPostFeatures($categoryId)->result->toReturn;
$sendGoodsAddressList = OpenAPI::getSendGoodsAddressList()->result->toReturn;
$freightTemplateList = OpenAPI::getFreightTemplateList()->result->toReturn;

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

.form-edit {
  max-width: 800px;
  padding: 15px;
  margin: 0 auto;
}
.form-edit-property-label {
  text-align: right;
  width: 100px;
}
.checkbox-wrapper {
  width: 100px;
  float: left;
  overflow: hidden;
}
</style>
</head>
<body>
<div class="container">
<form class="form-edit" name="mainform" action="offer_new.php" method="POST">
  <div class="panel panel-primary">
    <div class="panel-heading">填写基本信息</div>
    <table class="table">
      <tr>
        <td>标题:</td>
        <td colspan="2"><input style="width:600px" type="text" name="title" value="<?php echo($title); ?>"/></td>
      </tr>
      <tr>
        <td>产品属性:</td>
        <td colspan="2"></td>
      </tr>
      <?php
         foreach ($features as $feature) {
             echo('<tr><td></td><td class="form-edit-property-label">');
             echo($feature->name.'</td><td>');

             switch($feature->showType) {
                 case -1:
                     echo('<input type="number" name="feature-'.$feature->fid.'"/>');
                     break;
                 case 0:
                     echo('<input type="text" name="feature-'.$feature->fid.'"/>');
                     break;
                 case 1:
                     echo('<select name="feature-'.$feature->fid.'">');
                     foreach ($feature->featureIdValues as $value) {
                         echo('<option value="'.$value->value.'">'.$value->value.'</option>');
                     }
                     echo('</select>');
                     break;
                 case 2:
                     foreach ($feature->featureIdValues as $value) {
                         echo('<div class="checkbox-wrapper"><input type="checkbox" name="feature-'.$feature->fid.'-'.$value->value.'">'.$value->value.'</input></div>');
                     }
                     break;
                 case 3:
                     foreach ($feature->featureIdValues as $value) {
                         echo('<input type="radio" name="feature-'.$feature->fid.'" value="'.$value.'"/>');
                     }
                     break;
             }

             echo('</td></tr>');
         }
      ?>
    </table>
  </div>

  <div class="panel panel-primary">
    <div class="panel-heading">产品销售信息</div>
    <table class="table">
      <tr>
        <td>价格:</td>
        <td><input style="width: 600px;" type="text" name="price" value="<?php echo($price); ?>"/></td>
      </tr>
      <tr>
        <td>数量:</td>
        <td><input style="width: 600px;" type="text" name="amount" value="<?php echo($num); ?>"/></td>
      </tr>
    </table>
  </div>

  <div class="panel panel-primary">
    <div class="panel-heading">请给您的产品添加生动的图片和详细的说明</div>
    <table class="table">
      <tr>
        <td style="width: 111px;">产品主图:</td>
        <td></td>
      </tr>
      <tr>
        <td>放置相册:</td>
        <td>
          <select name="albumId">
            <?php
               foreach ($myAlbumList as $myAlbum) {
                   echo('<option value="'.$myAlbum->id.'">'.$myAlbum->name.'</option>');
               }
               foreach ($customAlbumList as $customAlbum) {
                   echo('<option value="'.$customAlbum->id.'">'.$customAlbum->name.'</option>');
               }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td></td>
        <td>
          <img style="width: 300px; height: 300px;" src="<?php echo($taobaoItem->pic_url); ?>"/>
        </td>
      </tr>
      <tr>
        <td>详情：</td>
        <td><textarea name="detail" value="<?php echo($taobaoItem->desc); ?>"></textarea></td>
      </tr>
    </table>
  </div>

  <div class="panel panel-primary">
    <div class="panel-heading">物流运费信息</div>
    <table class="table">
      <tr>
        <td>发货地址:</td>
        <td style="width: 600px;">
          <select name="sendGoodsAddressId">
            <?php
               foreach ($sendGoodsAddressList as $address) {
                   echo('<option value="'.$address->deliveryAddressId.'">'.$address->location.' '.$address->address.'</option>');
               }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td>运费设置:</td>
        <td>
          <select name="freightType">
            <option value="T">运费模版</option>
            <option value="F">卖家承担运费</option>
          </select>
          <select name="freightTemplateId">
            <?php
               foreach ($freightTemplateList as $template) {
                   echo('<option value="'.$template->id.'">'.$template->templateName.'</option>');
               }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td>单位重量:</td>
        <td>
          <input type="text" name="offerWeight"/>公斤/每件
        </td>
      </tr>
    </table>
  </div>

  <div class="panel panel-primary">
    <div class="panel-heading">特色服务</div>
    <table class="table">
      <tr>
        <td>混批:</td>
        <td style="width:600px"><input type="checkbox" name="mixWholeSale"/>支持混批</td>
      </tr>
    </table>
  </div>

  <input type="hidden" name="picUrl" value="<?php echo($picUrl); ?>"><br/>
  <input type="hidden" name="categoryId" value="<?php echo($categoryId); ?>"/>
  <input class="btn btn-lg btn-success btn-block" type="submit" value="一键上传宝贝"/>

</form>
</div>
<script>
var editor = CKEDITOR.replace('detail');
</script>
</body>
</html>
