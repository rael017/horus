<?php
namespace App\DataBase;

use \PDO;
class Crud
{
	
	
	private static $pdo;
	


	private static function conectar(){
		
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
	
	public static function Create($table,$values)
	{
		$keys = array_keys($values);
		$binds = array_pad([],count($keys),'?');
	
		
		$key = implode(',',$keys);
		
		$valor = implode(',',$binds);
		
		$sql = "INSERT INTO `$table` ($key) VALUES ($valor)";
		
	    self::execute($sql,array_values($values));
		
		return self::conectar()->lastInsertId();
	}
	
	
	public static function Ready($table, $where = null, $order = null, $limit = null, $filds = '*')
	{
		
		
		$where = strlen($where) ? ' WHERE '.$where : '';
		$order = strlen($order) ? ' ORDER BY '.$order : '';
		$limit = strlen($limit) ? ' LIMIT '.$limit : '';
		
		$sql = "SELECT $filds FROM  `$table` $where $order $limit";
	
		
		$base = self::execute($sql);
		
		return $base;
	}
	
	public static function Update($table,$where,$values)
	{
		$filds = array_keys($values);
		
		$valor = implode('=?,',$filds);
		$sql = "UPDATE `$table` SET $valor=? WHERE $where";
		
		self::execute($sql,array_values($values));
		return true;
	}
	
	public static function Delete($table,$where)
	{
		$sql = "DELETE FROM `$table` WHERE $where";
		
		self::execute($sql);
		return true;
	}

	public static function SelectInner($tableLeft, $tableRigth, $fields = '*', $fildLeft, $fildRigth ,$orderBy = null, $limit = null) {
		
		$order = strlen($orderBy) ? " ORDER BY $orderBy" : "";
		$limit = strlen($limit) ? " LIMIT $limit" : "";

		$sql = "SELECT $fields FROM `$tableRigth` JOIN `$tableLeft` ON $fildRigth = $fildLeft $order $limit";

	
		$base = self::execute($sql);
				
		return $base;
	
	}

	public static function createWithImage($table, $values, $imageFile, $imageFieldName)
    {
        // Verificar se o arquivo é uma imagem válida
        if (!self::isImageValid($imageFile)) {
            return false;
        }
        
        // Converter a imagem em dados binários
        $imageData = file_get_contents($imageFile['tmp_name']);
        $imageData = base64_encode($imageData);
        
        // Adicionar os dados da imagem ao array de valores
        $values[$imageFieldName] = $imageData;
        
        // Executar o método Create para inserir os valores no banco de dados
        return self::Create($table, $values);
    }
    private static function isImageValid($imageFile)
    {
        // Verificar se é um arquivo de imagem válido
        $allowedTypes = ['image/jpeg', 'image/png'];
        $fileType = $imageFile['type'];
        
        if (!in_array($fileType, $allowedTypes)) {
            return false;
        }
        
        return true;
    }

	// Função para criar tabelas
    public static function createTable($tableName, $columns) {
        $columnDefinitions = [];
        foreach ($columns as $column => $definition) {
            $columnDefinitions[] = "`$column` $definition";
        }
        $columnsSql = implode(', ', $columnDefinitions);

        $sql = "CREATE TABLE `$tableName` ($columnsSql)";
        
        self::execute($sql);
        return true;
    }

    // Função para apagar tabelas
    public static function dropTable($tableName) {
        $sql = "DROP TABLE IF EXISTS `$tableName`";
        
        self::execute($sql);
        return true;
    }
}

?>