<?php 

namespace App\includes;

ini_set('session.use_strict_mode', 1);

$secure = true; // if you only want to receive the cookie over HTTPS
$httponly = true; // prevent JavaScript access to session cookie
$samesite = 'Strict';

if(PHP_VERSION_ID < 70300) {
	session_set_cookie_params(1200, '/; samesite='.$samesite, $_SERVER['HTTP_HOST'], $secure, $httponly);
} else {
	session_set_cookie_params([
		'lifetime' => '',
		'path' => '/',
		'domain' => $_SERVER['HTTP_HOST'],
		'secure' => $secure,
		'httponly' => $httponly,
		'samesite' => $samesite
	]);
} 




?>
