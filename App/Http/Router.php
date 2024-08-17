<?php
 namespace App\Http;
 use \Closure;
 use \Exception;
 use \ReflectionFunction;
 use \App\Http\Middlewares\Config;

 class Router
 {
	 /**
	  * URL Completa Do Projeto (Raiz)
	  *@var string
	  */
	  
	  private $url = '';
	  
	  /**
	   * Perfixo Das Rotas
	   * @var string
	   */
	  
	  private $prefix = '';
	  
	  
	  /**
	   * Indice De Rotas
	   * @var array
	   */
	  
	  private $routes = [];
	   
	  
	  /**
	   * Instancia De Request
	   * @var Request
	   */
	  
	  private $request;
	  
	  private $contenType = 'text/html';

	  public function __construct($url)
	  {
		
		   $this->request = new Request($this);
		   $this->url = $url;
		   $this->setPrefix();
	  }

	  public function setContenType($contenType){
		$this->contenType = $contenType;
	  }
	   
	  /**
	   * Metodo Responsavel Por Definir O Prefixo Das Rotas
	   * @param string $url
	   */
	  private function setPrefix()
	  {
		  $parse_url = parse_url($this->url);
		  $this->prefix = $parse_url['path'] ?? '';
	  }
	  
	  /**
	   * Metodo Responsavel Por Adicionar Uma Rota Na Classe
	   * @param string $method
	   * @param string $route
	   * @param array $params
	   */
	  
	  private function addRoute($method,$route,$params = [])
	  {
		
		foreach($params as $key => $value){
			
			if($value instanceof Closure){
				$params['Controller'] = $value;

				unset($params[$key]);
				continue;
				
			}
			
		}
		$params['middlewares'] = $params['middlewares'] ?? [];
		
		
		$params['variables'] = [];
		
		$patternVariable = '/{(.*?)}/';
		if(preg_match_all($patternVariable,$route,$matches)){
			$route = preg_replace($patternVariable,'(.*?)',$route);
			$params['variables'] = $matches[1];
			
		}

	    $patternRoute = '/^'.str_replace('/','\/',$route).'$/';    
	
		$this->routes[$patternRoute][$method] = $params;
			
	  }
	  
	  /**
	   * Metodo Responsavel Por Definir Uma Rota De GET
	   * @param string $route
	   * @param array $params
	   */
	  
	  public function get($route, $params = [])
	  {
		 
		  return $this->addRoute('GET',$route,$params);
	  }
	  
	  
	   /**
	   * Metodo Responsavel Por Definir Uma Rota De POST
	   * @param string $route
	   * @param array $params
	   */
	  
	  public function post($route, $params = [])
	  {
		 
		  return $this->addRoute('POST',$route,$params);
	  }
	  
	   /**
	   * Metodo Responsavel Por Definir Uma Rota De PUT
	   * @param string $route
	   * @param array $params
	   */
	  
	  public function put($route, $params = [])
	  {
		 
		  return $this->addRoute('PUT',$route,$params);
	  }
	  
	    	  /**
	   * Metodo Responsavel Por Definir Uma Rota De DELETE
	   * @param string $route
	   * @param array $params
	   */
	  
	  public function delete($route, $params = [])
	  {
		 
		  return $this->addRoute('DELETE',$route,$params);
	  }
	  
	  /**
	   * Metodo Responsavel Por Retornar A Uri Sem O Prefixo
	   * @return string 
	   */
	  
	  private function getUri()
	  {
		 $uri = $this->request->getUri();
		 $xUri = strlen($this->prefix) ? explode($this->prefix,$uri) : [$uri];
		 
		 return end($xUri);
	  }
	  
	  /**
	   * Metodo Responsavel Por Retornar Os Dados Da Rota Atual
	   * @return array
	   */
	  
	  private function getRoute()
	  {
		$uri = $this->getUri();
		
		$httpMethod = $this->request->getMethodHttp();
		
		foreach($this->routes as $patternRoute => $methods){
			if(preg_match($patternRoute,$uri,$matches)){
				if(isset($methods[$httpMethod])){
					
					unset($matches[0]);
					
					$keys = $methods[$httpMethod]['variables'];
					$methods[$httpMethod]['variables'] = array_combine($keys,$matches);
					$methods[$httpMethod]['variables']['request'] = $this->request;
					return $methods[$httpMethod];
				}
			
			}
			
		}
		throw new Exception('Pagina Nao Existe',404);
		
	  }
	  
	  /**
	   * Metodo Responsavel Por Executar A Rota Atual
	   * @return Response
	   */
	  public function run()
	  {
		 try{
			$route = $this->getRoute();
		
			if(!isset($route['Controller'])){
				throw new Exception('A Pagina Nao Pode Ser Processada',500);
				
			}
			
			$args = [];
			$Reflection = new ReflectionFunction($route['Controller']);
			
			foreach($Reflection->getParameters() as $parameter){
				$name = $parameter->getName();
				$args[$name] = $route['variables'][$name] ?? '';
			}
			 $allowedOrigin = 'http://localhost:3000'; // Altere para sua origem específica
            if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] === $allowedOrigin) {
                header("Access-Control-Allow-Origin: " . $allowedOrigin);
                header("Access-Control-Allow-Methods: GET");
            }
			return (new Config($route['middlewares'],$route['Controller'],$args))->next($this->request);
			
		 }catch(Exception $e){
			return new Responce($e->getCode(), $this->getMenssageError($e->getMessage()),$this->contenType);
		 }
	  }

	  private function getMenssageError($menssage){
		switch($this->contenType){
			case 'application/json':
				return[
					'error'=>$menssage
				];
				break;
			default:
				return $menssage;
				break;
		}
	  }
	  
	  public function getCurrentUrl()
	  {
		$this->url.$this->getUri();
	  }
	  
	public function redirect($route)
	{
    // Definir a URL completa de redirecionamento
    $url = $this->url . $route;
    header('Location: ' . $url);
    exit; // Garantir que o script p


	}

	 /**
     * Metodo Responsavel Por Agrupar Rotas Comum
     * @param array $options
     * @param Closure $callback
     */
      public function group(array $options, Closure $callback)
     {
        // Salvar estado atual
        $prefixBackup = $this->prefix;
        $middlewaresBackup = $options['middlewares'] ?? [];

        // Configurar novo prefixo e middlewares
        if (isset($options['prefix'])) {
            $this->prefix .= $options['prefix'];
        }

        // Executar o callback com o grupo de rotas
        call_user_func($callback, $this);

        // Restaurar estado anterior
        $this->prefix = $prefixBackup;
        $options['middlewares'] = $middlewaresBackup;
    }
	
 }

?>