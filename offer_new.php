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
        
$offer = stripslashes(urldecode($_REQUEST['offer']));
$timestamp = time() * 1000;
$accessToken = $_SESSION['access_token'];

var_dump($offer);

$result = OpenAPI::offerNew($offer);

if ($result->result->success) {
    echo('上传成功');
} else {
    echo($result->message[0]);
}

?>
</body>
</html>