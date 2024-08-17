<?php

use \App\Http\Responce;
use \App\Controllers\Api;
$obRouter->get('/api/V1',[
	'middlewares'=>[
		'api'
	],
	function($request){
		return new Responce(200,Api\Api::getDetails($request),'application/json');
	}
]);

?>