<?php

namespace Core\Middlewares;
use Core\Middlewares\IMiddleware;
use Exception;

class Api implements IMiddleware
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