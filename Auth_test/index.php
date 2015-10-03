<?php

$auth = require_once '../Auth_v2/Base.php';

$auth->config(array(
	'makeLog' => true,
	'multiple' => false,
	'onMultiple' => 'discard'
	));

$auth->connect('localhost', 'auth', 'admin', 'admin');

// $auth->login('admin', 'admin');
// $auth->lockscreen('admin');
var_dump($auth->check());

if($auth->hasError())
	echo $auth->getStatus() . "<br><br>";


// Show log file contents
$f = fopen('../Auth_v2/log.txt', 'r+');
$line = '';
while (!feof($f)) {
	$line .= fgets($f) . "<br>";
}
echo $line;

// var_dump($_SESSION);
// var_dump($_COOKIE);

?>