<?php

require_once 'Util.class.php';

$state = stripslashes($_REQUEST['state']);
$signedUrl = Util::authSign($state);

header('Location: '.$signedUrl);

?>