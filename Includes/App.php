<?php 


use \Core\Utils\Envs;
use \Core\Utils\MainView;
use \Core\Middlewares\Config;
require( __DIR__.'/../vendor/autoload.php');



date_default_timezone_set('America/Sao_Paulo');

Envs::load(__DIR__.'/../');


define('URL',getenv('URL'));
define('BLOG',getenv('BLOG'));
define('BASE',getenv('BASE'));
define('DIR',getenv('DIR'));

define('DB_HOST',getenv('DB_HOST'));
define('DB_NAME',getenv('DB_NAME'));
define('DB_USER',getenv('DB_USER'));
define('DB_PASS',getenv('DB_PASS'));

MainView::init([
	"URL" =>URL,
	"BLOG"=>BLOG,
	"BASE"=>BASE,
	"DIR" =>DIR
]);

Config::setMap([
	'Manutention'=>\Core\Middlewares\Manutention::class,
	'api'=>\Core\Middlewares\Api::class,
	
	
]);

Config::setDefault([
	'Manutention'
]);
?>