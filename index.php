<?php
use  \Core\Http\Router;

include_once(__DIR__.'/Includes/App.php');


$obRouter = new Router(URL);



include_once(__DIR__.'/Routes/Pages.php');

$obRouter->run()->sendResponse();


?>