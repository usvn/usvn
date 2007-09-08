<?php
$user = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
$pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null;
$guser = isset($_GET['user']) ? $_GET['user'] : null;
$gpass = isset($_GET['pass']) ? $_GET['pass'] : null;
$method = isset($_GET['method']) ? $_GET['method'] : 'Basic';

if (! $user || ! $pass || $user != $guser || $pass != $gpass) {
	header('WWW-Authenticate: ' . $method . ' realm="ZendTest"');
    header('HTTP/1.0 401 Unauthorized');
}

echo serialize($_GET), "\n", $user, "\n", $pass, "\n";