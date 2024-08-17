<?php


use \App\Http\Responce;
use \App\Controllers\Admin;

$obRouter->get('/Admin',[
	
	function($request){
		return new Responce(200,Admin\Home::getHome($request));
	}
]);





?>