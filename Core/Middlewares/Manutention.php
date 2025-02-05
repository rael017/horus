<?php

namespace Core\Middlewares;

use Exception;

class Manutention
{
/**
 * metodo responsavel por executar o middleware
 * @param Request $request
 * @param Closure
 * @return Response 
 */

 public function handle($request, $next){
	if(getenv('MANUTENTION') == 'true'){
		throw new Exception('Pagina Em Manutenção, Tente Novamente Mais Tarde',200);
	}
	return $next($request);
 }
}

?>