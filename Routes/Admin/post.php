<?php


use \App\Http\Responce;
use \App\Controllers\Admin;

$obRouter->get('/Admin/noticias',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-redator'
	],
	function($request){
		return new Responce(200,Admin\Noticias::getPost($request));
	}
]);

$obRouter->get('/Admin/noticias/cadastrar-noticia',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-redator'
	],
	function($request){
		return new Responce(200,Admin\Noticias::getNewPost($request));
	}
]);

$obRouter->post('/Admin/noticias/cadastrar-noticia',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-redator'
	],
	function($request){
		return new Responce(200,Admin\Noticias::insertPost($request));
	}
]);

$obRouter->get('/Admin/noticias/cadastrar-categoria',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-redator'
	],
	function($request){
		return new Responce(200,Admin\Noticias::getNewCategory($request));
	}
]);

$obRouter->post('/Admin/noticias/cadastrar-categoria',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-redator'
	],
	function($request){
		return new Responce(200,Admin\Noticias::insertCategory($request));
	}
]);









?>