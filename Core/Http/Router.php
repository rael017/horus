<?php
 namespace Core\Http;
use Closure;
use ReflectionFunction;
use Core\Http\Exceptions\RouteNotFoundException;
use Core\Middlewares\MiddlewarePipeline;


class Router
{
    private array $routes = [];
    private string $prefix = '';
    private array $groupMiddleware = [];

    public function __construct(
        private Request $request,
        private string $baseUrl,
        private array $middlewareMap = [],
        private array $defaultMiddleware = []
    ){}

    private function addRoute(string $method, string $route, array $params): void
    {
        $route = rtrim($this->prefix, '/') . '/' . ltrim($route, '/');
        
        // MUDANÇA: Aceita tanto Closure quanto [Classe, Metodo]
        $params['controller'] = $params[0] ?? $params['controller'] ?? null;
        unset($params[0]);

        if (!($params['controller'] instanceof Closure) && !is_array($params['controller'])) {
             throw new \InvalidArgumentException("O controlador da rota deve ser uma Closure ou um array [Controller::class, 'metodo'].");
        }

        $routeMiddleware = $params['middlewares'] ?? [];
        $params['middlewares'] = array_merge($this->defaultMiddleware, $this->groupMiddleware, $routeMiddleware);
        
        $params['variables'] = [];
        $patternVariable = '/\{([a-zA-Z0-9_]+)\}/';
        if (preg_match_all($patternVariable, $route, $matches)) {
            $route = preg_replace($patternVariable, '([a-zA-Z0-9_.-]+)', $route);
            $params['variables'] = $matches[1];
        }

        $patternRoute = '#^' . $route . '$#';
        $this->routes[$patternRoute][$method] = $params;
    }

    public function get(string $route, array $params): void { $this->addRoute('GET', $route, $params); }
    public function post(string $route, array $params): void { $this->addRoute('POST', $route, $params); }
    public function put(string $route, array $params): void { $this->addRoute('PUT', $route, $params); }
    public function delete(string $route, array $params): void { $this->addRoute('DELETE', $route, $params); }

    public function group(array $options, Closure $callback): void
    {
        $prefixBackup = $this->prefix;
        $middlewareBackup = $this->groupMiddleware;

        $this->prefix .= $options['prefix'] ?? '';
        $this->groupMiddleware = array_merge($this->groupMiddleware, $options['middlewares'] ?? []);
        $callback($this);

        $this->prefix = $prefixBackup;
        $this->groupMiddleware = $middlewareBackup;
    }

    private function getUri(): string
    {
        $uri = $this->request->uri;
        $baseUrlPath = parse_url($this->baseUrl, PHP_URL_PATH) ?? '';
        if ($baseUrlPath && str_starts_with($uri, $baseUrlPath)) {
            return substr($uri, strlen($baseUrlPath)) ?: '/';
        }
        return $uri;
    }
    
    private function findRoute(): array
    {
        $uri = $this->getUri();
        $httpMethod = $this->request->httpMethod;

        foreach ($this->routes as $patternRoute => $methods) {
            if (preg_match($patternRoute, $uri, $matches)) {
                if (isset($methods[$httpMethod])) {
                    array_shift($matches);
                    $routeParams = $methods[$httpMethod];
                    $routeParams['variables'] = array_combine($routeParams['variables'], $matches);
                    return $routeParams;
                }
            }
        }
        // Lança exceção com informações de depuração
        throw new RouteNotFoundException('Route not found.', [
            'uri' => $uri,
            'method' => $httpMethod
        ]);
    }

    public function run(): Response
    {
        try {
            $route = $this->findRoute();
            $controller = $route['controller'];
            
            // MUDANÇA: A lógica de preparação de argumentos foi melhorada.
            $args = [];
            if ($controller instanceof Closure) {
                $reflection = new ReflectionFunction($controller);
                foreach ($reflection->getParameters() as $parameter) {
                    $name = $parameter->getName();
                    $type = $parameter->getType();

                    // Prioridade 1: Injeta parâmetros da URL (ex: /users/{id})
                    if (isset($route['variables'][$name])) {
                        $args[$name] = $route['variables'][$name];
                        continue;
                    }

                    // Prioridade 2: Injeta a classe Request por type-hint
                    if ($type && $type->getName() === Request::class) {
                        $args[$name] = $this->request;
                        continue;
                    }
                    
                    // CORREÇÃO: Injeta a classe Request por convenção de nome ('request')
                    if ($name === 'request') {
                         $args[$name] = $this->request;
                         continue;
                    }
                }
            } else {
                // Para controllers de classe, os argumentos da URL são passados diretamente.
                $args = $route['variables'];
            }
            
            $pipeline = new MiddlewarePipeline(
                $route['middlewares'],
                $controller,
                $args,
                $this->middlewareMap
            );

            $responseContent = $pipeline->next($this->request);

            if ($responseContent instanceof Response) {
                return $responseContent;
            }

            return new Response($responseContent);

        } catch (RouteNotFoundException $e) {
            // Exibe informações de depuração para o erro 404
            $is_debug_mode = defined('APP_DEBUG') && APP_DEBUG === true;
            $debugInfo = $e->getDebugInfo();
            $errorMessage = "<h1>404 - Not Found</h1><p>A página que você procura não foi encontrada.</p>";
            if ($is_debug_mode) {
                $errorMessage .= "<hr><pre><b>DEBUG INFO:</b><br>";
                $errorMessage .= "<b>URI Tentada:</b> " . htmlspecialchars($debugInfo['uri']) . "<br>";
                $errorMessage .= "<b>Método:</b> " . htmlspecialchars($debugInfo['method']) . "</pre>";
            }
            return new Response($errorMessage, $e->getCode());

        } catch (\Exception $e) {
            // Loga o erro para referência futura
            error_log($e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());

            // Exibe erros detalhados em modo de depuração para facilitar a resolução de problemas.
            $is_debug_mode = defined('APP_DEBUG') && APP_DEBUG === true;
            $errorMessage = $is_debug_mode
                ? "<h1>500 - Erro Interno do Servidor</h1><pre><b>Erro:</b> {$e->getMessage()}<br><b>Arquivo:</b> {$e->getFile()}<br><b>Linha:</b> {$e->getLine()}</pre>"
                : "<h1>500 - Erro Interno do Servidor</h1><p>Ocorreu um erro inesperado.</p>";

            return new Response($errorMessage, 500);
        }
    }
}
?>