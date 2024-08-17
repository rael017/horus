<?php
namespace App\Http\Middlewares;

use \Closure;


class Config
{ 
	/**
	 * mapeamento do middleware
	 */
	
	private static $map = [];
	/**
	 * Fila de middlewares a serem executados
	 * @var array
	 */
	
	 /** 
	  * Carregamento de middlewares que seram cerregados em todas as rotas 
	  */
	  
	private static $default = [];
	
	/**
	 * Fila de middlewares a serem executados
	 */
	private $middlewares = [];
	 
	 /**
	  * Função  de execução do controlador
	  *@var Closure
	  */
	private $controller;
	  
	  
	  /**
	   * argumentos da função do controller
	   * @var array
	   */
	  
	private $controllerArgs = [];
	  
	  /**
	   * metodo responsavel pela fila de middlewares
	   * @param array $middleares
	   * @param Closure
	   * @param array $controllerArgs
	   */
	  
	public function __construct($middlewares,$controller,$controllerArgs)
	  {
		$this->middlewares     = array_merge(self::$default,$middlewares);
		$this->controller     = $controller;
		$this->controllerArgs = $controllerArgs;
	  }
	  
	  /**
	   * metodo rsponsavel por definir o mapeamento de middlewares
	   * @var $map
	   */
	  
	public static function setMap($map){
		self::$map = $map;
	  }
	  
	  /**
	   * metodo rsponsavel por definir o mapeamento de middlewares padrões
	   * @var $default
	   */
	  
	public static function setDefault($default){
		self::$default = $default;
	  }
	  
	public function next($request){
		if(empty($this->middlewares)) return call_user_func_array($this->controller,$this->controllerArgs);
		
		$middleware = array_shift($this->middlewares);
		if(!isset(self::$map[$middleware])){
			throw new \Exception('Problema interno da aplicação',500);
		}
		
		$Config = $this;
		$next = function($request) use($Config){
			return $Config->next($request);
		};
		
		
		return (new self::$map[$middleware])->handle($request,$next);
		
	  }
	  
}
?>