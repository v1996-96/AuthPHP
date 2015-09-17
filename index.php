<?php

$auth = require_once 'Auth/Main.php';
$captcha = require_once 'Auth/Captcha.php';

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

$cFlag = false;

if (isset($_POST['login']) && isset($_POST['pwd'])) {
	if (isset($_POST['captcha'])) {
		if ($captcha->check($_POST['captcha'])) {
			$cFlag = false;
			$auth->login($_POST['login'], $_POST['pwd'], isset($_POST['remember']));
		} else {
			$cFlag = true;
		}
	} else {
		$auth->login($_POST['login'], $_POST['pwd'], isset($_POST['remember']));
	}
} else {
	if ($auth->check()) 
		$auth->reroute( $auth->successUrl );
}

var_dump($auth->getStatus());
var_dump($auth->getMessages());

?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>login application</title>

	<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>

	<div class="wrap">
		<h3>Форма авторизации</h3>
		<?php if ($auth->hasError() || $cFlag): ?>
		<div class="alert">
			<?= $auth->getStatus(); ?>
			<?= $cFlag?'Wrong captcha':''; ?>
		</div>
		<?php endif; ?>
		<form method="POST">
			<input class="text" name="login" type="text" placeholder="Логин" />
			<input class="text" name="pwd" type="password" placeholder="Пароль" />

			<div class="captcha">
				<a href="javascript:void(0);" onclick="document.getElementById('captcha').src='captcha.php';"><img id="captcha" src="captcha.php"/></a>
				<input class="text" name="captcha" type="text" placeholder="Капча" />
			</div>

			<label>
				<input type="checkbox" name="remember" value="1" />
				Запомнить
			</label>
			<button type="submit" name="action" value="login">Войти</button>
		</form>
	</div>

</body>
</html>