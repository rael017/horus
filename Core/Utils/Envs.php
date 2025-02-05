<?php

namespace Core\Utils;

class Envs
{
	/**
	 * Metodo esponsavel por carregar as variaveis de ambiente do projeto
	 * @param $dir caminho absoluto da pasta onde se encontra o arquivo .env
	 */
	
	 public static function load($dir){
		if(!file_exists($dir.'/.env')){
			return false;
		}
		
		$lines = file($dir.'/.env');
		foreach($lines as $line){
			putenv(trim($line));
		}
	 }
}

?>