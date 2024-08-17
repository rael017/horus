<?php
use \App\Http\Responce;
use \App\Controllers\Admin;

$obRouter->get('/Admin/login',[
	'middlewares'=>[
		'requiered-admin-logout'
	],
	function($request){
		return new Responce(200,Admin\Login::getLogin($request));
	}
]);

$obRouter->post('/Admin/login',[
	'middlewares'=>[
		'requiered-admin-logout'
	],
	function($request){
		return new Responce(200,Admin\Login::setLogin($request));
	}
]);

$obRouter->get('/Admin/logout',[
	'middlewares'=>[
		'requiered-admin-login'
	],
	function($request){
		
		return new Responce(200,Admin\Login::setLogout($request));
	}
]);

$obRouter->get('/Admin/confirm', [
    'middlewares' => [
        'requiered-admin-logout'
    ],
    function($request){
        return new Responce(200, Admin\Login::getConfirm($request));
    }
]);

$obRouter->post('/Admin/confirm', [
    'middlewares' => [
        'requiered-admin-logout'
    ],
    function($request){
        return new Responce(200, Admin\Login::setConfirm($request, 'Por favor, insira o código de confirmação.'));
    }
]);
?>