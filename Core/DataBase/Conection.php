<?php
namespace Core\DataBase;

use \PDO;
class Conection

{
	
	
	private static $pdo;
	


	protected static function conectar(){
		
		try{
			self::$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME,DB_USER,DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			self::$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		}catch(\PDOException $e){
			die('<pre>Erro'.$e->getMessage().'Ao Conectar</pre>');
			error_log($e->getMessage());
		}
		
		return self::$pdo;
		
		
	}
	
	protected static function execute($sql,$params = []){
		try{
			$statement = self::conectar()->prepare($sql);
			$statement->execute($params);
			return $statement;
		}catch(\PDOException $e){
			die('<pre>Erro'.$e->getMessage().'Ao Executar</pre>');
			error_log($e->getMessage());
		}
		
	}
}