<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use \Core\Utils\Envs;
use \Core\Utils\MainView;
use \Core\Http\Request;
use \Core\Middlewares\MiddlewarePipeline;
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
define('APP_DEBUG', true);

MainView::init([
	"URL" =>URL,
	"BLOG"=>BLOG,
	"BASE"=>BASE,
	"DIR" =>DIR
]);

// 1. Defina o mapa de middlewares (apelido => classe)
$middlewareMap = [
    'Manutention' => \Core\Middlewares\Manutention::class,
    'api'         => \Core\Middlewares\Api::class,
];

// 2. Defina os middlewares que rodarão em TODAS as rotas
$defaultMiddlewares = [
    'Manutention'
];

// 3. Crie a requisição
$request = new Request();
?>