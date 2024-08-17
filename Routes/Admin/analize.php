<?php


use \App\Http\Responce;
use \App\Controllers\Admin;



$obRouter->get('/Admin/analize',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-sub-administrador'
		
	],
	function($request){
		return new Responce(200,Admin\Analize::getPost($request));
	}
]);

$obRouter->post('/Admin/analize',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-sub-administrador'
	],
	function($request){
		return new Responce(200,Admin\Analize::setPost($request));
	}
]);

$obRouter->get('/Admin/analize/analizar-noticias',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-sub-administrador'
	],
	function($request){
		return new Responce(200,Admin\Analize::getPostAnalize($request));
	}
]);


$obRouter->get('/Admin/analize/{id}/editar',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-sub-administrador'
	],
	function($request,$id){
		return new Responce(200,Admin\Analize::getEditPost($request,$id));
	}
]);

$obRouter->post('/Admin/analize/{id}/editar',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-sub-administrador'
	],
	function($request,$id){
		return new Responce(200,Admin\Analize::setEditPost($request,$id));
	}
]);
$obRouter->get('/Admin/analize/{id}/excluir',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-sub-administrador'
	],
	function($request,$id){
		return new Responce(200,Admin\Analize::getDeletePost($request,$id));
	}
]);

$obRouter->post('/Admin/analize/{id}/excluir',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-sub-administrador'
	],
	function($request,$id){
		return new Responce(200,Admin\Analize::setDeletePost($request,$id));
	}
]);


