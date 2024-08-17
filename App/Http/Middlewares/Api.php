<?php

namespace App\Http\Middlewares;

use Exception;

class Api
{
/**
 * metodo responsavel por executar o middleware
 * @param Request $request
 * @param Closure
 * @return Response 
 */

 public function handle($request, $next){
    $request->getRoute()->setContenType('application/json');
    return $next($request);
 }
}

?>