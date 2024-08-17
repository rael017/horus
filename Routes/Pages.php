<?php 

use \App\Http\Responce;
use \App\Controllers\Pages;


$obRouter->get('/',[
	function($request){
		return new Responce(200,Pages\Home::getIndex($request));
	}
]);

$obRouter->post('/',[
	function($request){
		return new Responce(200,Pages\Home::setClient($request));
	}
]);
?>