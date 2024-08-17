<?php

use \App\Http\Responce;
use \App\Controllers\Api;
$obRouter->get('/api/V1/imagens/{fileName}',[
	'middlewares'=>[
		'api'
		
	],
	function($request,$fileName){
		return new Responce(200,Api\Imagens::getImage($request,$fileName));
	}
]);