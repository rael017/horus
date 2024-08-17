<?php


use \App\Http\Responce;
use \App\Controllers\Admin;

$obRouter->get('/Admin/usuarios',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-administrador-geral'
	],
	function($request){
		return new Responce(200,Admin\Usuarios::getUsers($request));
	}
]);

$obRouter->get('/Admin/verificar',[
	
	function($request){
		return new Responce(200,Admin\Usuarios::getVerification($request));
	}
]);

$obRouter->get('/Admin/verificar',[
	
	function($request){
		return new Responce(200,Admin\Usuarios::setVerification($request));
	}
]);

$obRouter->get('/Admin/usuarios/adicionar',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-administrador-geral'
	],
	function($request){
		return new Responce(200,Admin\Usuarios::getNewUser($request));
	}
]);

$obRouter->post('/Admin/usuarios/adicionar',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-administrador-geral'
	],
	function($request){
		return new Responce(200,Admin\Usuarios::setNewUser($request));
	}
]);


$obRouter->get('/Admin/usuarios/{id}/editar',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-administrador-geral'
	],
	function($request,$id){
		return new Responce(200,Admin\Usuarios::getEditUser($request,$id));
	}
]);

$obRouter->post('/Admin/usuarios/{id}/editar',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-administrador-geral'
	],
	function($request,$id){
		return new Responce(200,Admin\Usuarios::setEditUser($request,$id));
	}
]);

$obRouter->get('/Admin/usuarios/{id}/excluir',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-administrador-geral'
	],
	function($request,$id){
		return new Responce(200,Admin\Usuarios::getDeleteUser($request,$id));
	}
]);

$obRouter->post('/Admin/usuarios/{id}/excluir',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-administrador-geral'
	],
	function($request,$id){
		return new Responce(200,Admin\Usuarios::setDeleteUser($request,$id));
	}
]);



