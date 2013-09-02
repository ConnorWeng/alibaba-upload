<?php

require_once 'Util.class.php';

$url = $_REQUEST['url'];
$state = stripslashes($_REQUEST['state']);

$signedUrl = Util::authSign($state);

header('Location: '.$signedUrl);

?>