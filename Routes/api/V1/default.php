<?php

use \Core\Http\Response;
use \App\Controllers\Api;
$obRouter->get('/api/V1',[
	'middlewares'=>[
		'api'
	],
	function($request){
		return new Response(200,Api\Api::getDetails($request),'application/json');
	}
]);

?>