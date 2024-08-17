<?php


use \App\Http\Responce;
use \App\Controllers\Admin;

$obRouter->get('/Admin/gerenciar/site',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-administrador-geral'
	],
	function($request){
		return new Responce(200,Admin\Gerencia::getSite($request));
	}
]);

$obRouter->get('/Admin/gerenciar/site/adicionar',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-administrador-geral'
	],
	function($request){
		return new Responce(200,Admin\Gerencia::getNewPage($request));
	}
]);

$obRouter->post('/Admin/gerenciar/site/adicionar',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-administrador-geral'
	],
	function($request){
		return new Responce(200,Admin\Gerencia::setNewPage($request));
	}
]);


$obRouter->get('/Admin/gerenciar/site/{id}/editar',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-administrador-geral'
	],
	function($request,$id){
		return new Responce(200,Admin\Gerencia::getEditPage($request,$id));
	}
]);


$obRouter->post('/Admin/gerenciar/site/{id}/editar',[
	'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-administrador-geral'
	],
	function($request,$id){
		return new Responce(200,Admin\Gerencia::setEditPage($request,$id));
	}
]);

// Rota GET para arquivar uma página por ID

$obRouter->get('/Admin/gerenciar/site/{id}/arquivar', [
    'middlewares'=>[
		'load-authenticated-user',
		'requiered-admin-login',
		'check-administrador-geral'
    ],
    function($request, $id) {
        return new Responce(200,Admin\Gerencia::getArchivePage($request, $id));
    }
]);
 


 



