<?php
require_once 'Config.class.php';
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script>
function s() {
    window.location = 'sign.php?state=' + document.mainform.id.value;
}
</script>  
</head>
<body>
<center>
<div>
<form name="mainform" action="" method="POST">
淘宝宝贝id:<input type="textarea" name="id" value="26896652760"/>
    <input type="button" value="一键上传宝贝" onclick="s();"/>
</form>
</div>
<center>
</body>
</html>
