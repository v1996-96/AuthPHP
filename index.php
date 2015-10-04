<?php

// Get plugin instance
$auth = require_once 'Auth_v2/Base.php';

// Make some configurations
$auth->iniConfig('config.ini');

// Connect to database
$auth->connect('localhost', 'auth', 'admin', 'admin');


// Log in the user
if (isset($_POST["login"]) &&
	isset($_POST["pwd"])) {

	$auth->login($_POST["login"], $_POST["pwd"], isset($_POST["remember"]));
} else {

	$auth->check();
}

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
		<?php if ($auth->hasError()): ?>
		<div class="alert">
			<?= $auth->getStatus(); ?>
		</div>
		<?php endif; ?>
		<form method="POST">
			<input class="text" name="login" type="text" placeholder="Логин" />
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