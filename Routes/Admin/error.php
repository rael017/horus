<?php


use \App\Http\Responce;
use \App\Controllers\Admin;

$obRouter->get('/Admin/error',[
	'middlewares'=>[
		'requiered-admin-login'
	],
	function($request){
		return new Responce(403,Admin\Error::getError($request));
	}
]);

?>