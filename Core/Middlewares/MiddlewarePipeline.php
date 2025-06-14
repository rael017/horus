<?php
namespace Core\Middlewares;

use Core\Http\Request;
use Core\Http\Response;
use Core\Middlewares\IMiddleware;
use \Closure;
use \Exception;
use \ReflectionMethod;

class MiddlewarePipeline
{
    private array $queue = [];

    /**
     * @param array $middlewares Array de apelidos de middlewares.
     * @param Closure|array $controller O handler final da rota.
     * @param array $controllerArgs Argumentos para o handler.
     * @param array $middlewareMap Mapa de apelidos para classes de middleware.
     */
    public function __construct(
        private array $middlewares,
        private Closure|array $controller,
        private array $controllerArgs,
        private array $middlewareMap
    ) {
        $this->queue = $this->middlewares;
    }

    /**
     * Executa o próximo item na fila de middlewares.
     */
    public function next(Request $request)
    {
        // Se a fila de middlewares estiver vazia, executa o controller final
        if (empty($this->queue)) {
            // Se o controller for um array [classe, metodo]
            if(is_array($this->controller)) {
                [$class, $method] = $this->controller;
                $controllerInstance = new $class();
                
                // Injeta dependências (Request) e parâmetros da rota no método
                $reflection = new ReflectionMethod($controllerInstance, $method);
                $finalArgs = [];
                 foreach ($reflection->getParameters() as $parameter) {
                    $name = $parameter->getName();
                    $type = $parameter->getType();
                    if ($type && $type->getName() === Request::class) {
                        $finalArgs[] = $request;
                    } elseif (isset($this->controllerArgs[$name])) {
                        $finalArgs[] = $this->controllerArgs[$name];
                    }
                }
                return call_user_func_array([$controllerInstance, $method], $finalArgs);
            }
            // Se for uma Closure
            return call_user_func_array($this->controller, $this->controllerArgs);
        }

        $middlewareAlias = array_shift($this->queue);
        
        if (!isset($this->middlewareMap[$middlewareAlias])) {
            throw new Exception("Middleware não encontrado: '$middlewareAlias'", 500);
        }

        $middlewareClass = $this->middlewareMap[$middlewareAlias];
        $middlewareInstance = new $middlewareClass;
        
        if (!$middlewareInstance instanceof IMiddleware) {
             throw new Exception("A classe $middlewareClass precisa implementar a interface IMiddleware.", 500);
        }

        $next = fn(Request $request) => $this->next($request);
        return $middlewareInstance->handle($request, $next);
    }
}