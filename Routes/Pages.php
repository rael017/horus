<?php 
use \Core\Http\Response;
use \App\Controllers\Web;


$obRouter->get('/',[
	function($request){
		return new Response(200,Web\Home::getIndex($request));
	}
]);

?>