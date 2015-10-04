<?php

setcookie('referer', '/index.php', time(0) + 3600, '/');

$auth = require_once 'Auth/Main.php';

$auth->connect('localhost', 'auth', 'admin', 'admin');

$auth->config(array(
	'checkIP'			=> 'strict', # strict | acceptable | to_lockscreen
	'multiple'			=> false, 	 # allow multiple connections
	'onMultiple'		=> 'allow',  # allow | discard  only if multiple == false
	'reroute'           => true,
	'lockscreen'        => true,
	'loginPageUrl'      => '/index.php',
	'lockscreenPageUrl' => '/lockscreen.php',
	'successUrl'        => '/page.php',
	'lockRef'			=> false,
	'lockRef_Name'		=> 'referer'
	));

$auth->check();

var_dump($auth->getStatus());
var_dump($auth->getMessages());

?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<title>Комната, в которую попадают избранные</title>

	<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>

	<h1>
		Здравствуй, Нео!
	</h1>

</body>
</html>