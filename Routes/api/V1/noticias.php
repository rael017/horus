<?php

use \App\Http\Responce;
use \App\Controllers\Api;

$obRouter->get('/api/V1/noticias/',[
	'middlewares'=>[
		'api'
	],
	function($request){
		return new Responce(200,Api\Noticias::getPost($request),'application/json');
	}
]);

$obRouter->get('/api/V1/noticias/categoria/{category}',[
	'middlewares'=>[
		'api'
	],
	function($request,$category){
		return new Responce(200,Api\Noticias::getPostCategory($request,$category),'application/json');
	}
]);

$obRouter->get('/api/V1/noticias/busca/{arg}',[
	'middlewares'=>[
		'api'
	],
	function($request,$arg){
		return new Responce(200,Api\Noticias::getPostOfSearch($request,$arg),'application/json');
	}
]);

$obRouter->get('/api/V1/noticias/{id}',[
	'middlewares'=>[
		'api'
	],
	function($request,$id){
		return new Responce(200,Api\Noticias::getPostSingle($request,$id),'application/json');
	}
]);



$obRouter->post('/api/V1/noticias/',[
	'middlewares'=>[
		'api',
		'user-basic-auth'
	],
	function($request){
		return new Responce(201,Api\Noticias::setNewPost($request),'application/json');
	}
]);

$obRouter->put('/api/V1/noticias/{id}',[
	'middlewares'=>[
		'api',
		'user-basic-auth'
	],
	function($request,$id){
		return new Responce(201,Api\Noticias::setEditPost($request,$id),'application/json');
	}
]);
?>