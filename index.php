<?php
use  \App\Http\Router;

include_once(__DIR__.'/Includes/App.php');
include_once(__DIR__.'/Includes/Exemple-App.php');

$obRouter = new Router(URL);



include_once(__DIR__.'/Routes/Pages.php');
include_once(__DIR__.'/Routes/Admin.php');
include_once(__DIR__.'/Routes/Api.php');


$obRouter->run()->sendResponse();


?>