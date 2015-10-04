<?php

setcookie('referer', '/index.php', time(0) + 3600, '/');

// Define relative path to plugin
define("__AUTH_REFERANCE__", "Auth_v2/");

// Get plugin instance
$auth = require_once 'Auth_v2/Base.php';

// Make some configurations
$auth->iniConfig('config.ini');

// Connect to database
$auth->connect('localhost', 'auth', 'admin', 'admin');



if (isset($_POST["action"]) && $_POST["action"] == "logout"){
	$auth->full_logOut();
} else {
	$auth->check();
}

if ($auth->hasError()){
	echo $auth->getStatus();
}

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

	<div class="wrap">
		<h3>Действия</h3>
		<form method="POST">
			<button type="submit" name="action" value="logout">Выйти</button>
		</form>
	</div>

</body>
</html>