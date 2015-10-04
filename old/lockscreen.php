<?php

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

if(isset($_POST['pwd'])){
	$auth->lockscreen($_POST['pwd'], isset($_POST['remember']));
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
		<h3>Страница блокировки</h3>
		<?php if ($auth->hasError()): ?>
		<div class="alert">
			<?= $auth->getStatus(); ?>
		</div>
		<?php endif; ?>
		<form method="POST">
			<input type="hidden" name="lockscreen" value="1" />
			<input class="text" name="pwd" type="password" placeholder="Пароль" />
			<label>
				<input type="checkbox" name="remember" value="1" />
				Запомнить
			</label>
			<button type="submit" name="action" value="login">Войти</button>
		</form>
	</div>

</body>
</html>