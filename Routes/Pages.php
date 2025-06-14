<?php 
use \Core\Http\Response;
use \App\Controllers\Web;


$obRouter->get('/',[
	function($request){
		 
        // Chama o método estático diretamente
        $conteudo = Web\Home::getIndex($request); 

        // Retorna a resposta
        return new Response($conteudo, 200);
	}
]);

?>