<?php

use \App\Http\Responce;
use \App\Controllers\Admin;

$obRouter->get('/Admin/menssagens',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-sub-administrador'
	],
	function($request){
		return new Responce(200,Admin\Menssagens::getMsg($request));
	}
]);


$obRouter->get('/Admin/menssagens/{id}/response',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-sub-administrador'
	],
	function($request,$id){
		return new Responce(200,Admin\Menssagens::getSingleMsg($request,$id));
	}
]);
?>