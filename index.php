<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script>
function s() {
    var url_template = "http://gw.open.1688.com/auth/authorize.htm?client_id=1006478&site=china&redirect_uri=http%3a%2f%2flocalhost%2fphp%2fauth.php&state=YOUR_PARM&_aop_signature=DC667B0B8D0A5ADA0A43877803CCC3292CF1521A",
    url = url_template.replace('YOUR_PARM', document.mainform.title.value);
    window.location = 'sign.php?url=' + url;
}
</script>  
</head>
<body>
<center>
<div>
<form name="mainform" action="http://gw.open.1688.com/auth/authorize.htm?client_id=1006478&site=china&redirect_uri=http%3a%2f%2flocalhost%2fphp%2fauth.php&state=YOUR_PARM&_aop_signature=DC667B0B8D0A5ADA0A43877803CCC3292CF1521A" method="POST">
标题:<input type="textarea" name="title" value="面西"/>
    <input type="button" value="一键上传宝贝" onclick="s();"/>
</form>
</div>
<center>
</body>
</html>
