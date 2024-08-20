<?php

use \App\Utils\Envs;
use \App\Utils\MainView;
use \App\Http\Middlewares\Config;
require( __DIR__.'/../vendor/autoload.php');



date_default_timezone_set('America/Sao_Paulo');

Envs::load(__DIR__.'/../');

define('URL',getenv('URL'));
define('BASE',getenv('BASE'));
define('DIR',getenv('DIR'));

define('DB_HOST',getenv('DB_HOST'));
define('DB_NAME',getenv('DB_NAME'));
define('DB_USER',getenv('DB_USER'));
define('DB_PASS',getenv('DB_PASS'));

MainView::init([
	"URL" =>URL,
	"BASE"=>BASE,
	"DIR" =>DIR
]);

Config::setMap([
	'Manutention'=>\App\Http\Middlewares\Manutention::class,
	'api'=>\App\Http\Middlewares\Api::class
	
]);

Config::setDefault([
	'Manutention'
]);
?>

