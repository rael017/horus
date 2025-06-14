<?php 
namespace Core\Utils;


class MainView
{ 
	
	public static $vars;
	
	public static function init($vars = [])
	{
		self::$vars = $vars;
	}
	/**
	 * Retorna o conteudo da view
	 * @param string $view
	 * @return string
	 */
	private static function getContentView($view)
	{
		$file = __DIR__.'/../../Resources/'.$view.'.html';
		return file_exists($file) ? file_get_contents($file) : '';
	}
	
	/**
	 * Retorna a view Renderizada
	 * @param string $view
	 * @param array $vars(string/numeric)
	 * @return string
	 */
	public static function render($view, $vars = [])
	{
		
		
		$ContentView = self::getContentView($view);
		
		$vars = array_merge(self::$vars,$vars);
		
		$keys = array_keys($vars);		
		$keys = array_map(function($item){
			return "{{".$item."}}";
		},$keys);
	
		return str_replace($keys,array_values($vars),$ContentView) ;
	
	}
	
	
	
	
}
?>