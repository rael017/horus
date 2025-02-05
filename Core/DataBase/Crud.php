<?php
namespace Core\DataBase;

use \PDO;
use \Exception;
use \PDOException;
class Crud extends Conection
{
	
	
	
	public static function Create($table,$values)
	{
		$keys = array_keys($values);
		$binds = array_pad([],count($keys),'?');
	
		
		$key = implode(',',$keys);
		
		$valor = implode(',',$binds);
		
		$sql = "INSERT INTO `$table` ($key) VALUES ($valor)";
		
	    parent::execute($sql,array_values($values));
		
		return parent::conectar()->lastInsertId();
	}
	
	
	public static function Ready($table, $where = null, $order = null, $limit = null, $filds = '*')
	{
		
		
		$where = strlen($where) ? ' WHERE '.$where : '';
		$order = strlen($order) ? ' ORDER BY '.$order : '';
		$limit = strlen($limit) ? ' LIMIT '.$limit : '';
		
		$sql = "SELECT $filds FROM  `$table` $where $order $limit";
	
		
		$base = parent::execute($sql);
		
		return $base;
	}
	
	public static function Update($table,$where,$values)
	{
		$filds = array_keys($values);
		
		$valor = implode('=?,',$filds);
		$sql = "UPDATE `$table` SET $valor=? WHERE $where";
		
		parent::execute($sql,array_values($values));
		return true;
	}
	
	public static function Delete($table,$where)
	{
		$sql = "DELETE FROM `$table` WHERE $where";
		
		parent::execute($sql);
		return true;
	}

	public static function SelectInner($tableLeft, $tableRigth, $fields = '*', $fildLeft, $fildRigth ,$orderBy = null, $limit = null) {
		
		$order = strlen($orderBy) ? " ORDER BY $orderBy" : "";
		$limit = strlen($limit) ? " LIMIT $limit" : "";

		$sql = "SELECT $fields FROM `$tableRigth` JOIN `$tableLeft` ON $fildRigth = $fildLeft $order $limit";

	
		$base = parent::execute($sql);
				
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

	public static function Describe($table)
	{
		try {
			$query = parent::execute("DESCRIBE `$table`");
			$columns = $query->fetchAll(\PDO::FETCH_ASSOC);
			return $columns;
		} catch (PDOException $e) {
			// Caso a tabela não exista, o erro será tratado aqui
			if ($e->getCode() == '42S02') {
				return false; // Retorna falso quando a tabela não é encontrada
			}
			throw $e; // Relança a exceção para outros erros
		}
	}

	public static function tableExists($tableName)
{
    try {
        $sql = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :tableName AND TABLE_SCHEMA = :database";
        $stmt = parent::conectar()->prepare($sql);
        $stmt->execute([
            ':tableName' => $tableName,
            ':database' => 'testes', // Substitua pelo nome do seu banco de dados
        ]);
        
        $count = $stmt->fetchColumn();
        
        // Verifica se a tabela existe
        if ($count > 0) {
            echo "Tabela '$tableName' já existe.\n";
            return true;
        } else {
            echo "Tabela '$tableName' não encontrada.\n";
            return false;
        }
    } catch (\Exception $e) {
        echo "Erro ao verificar tabela '$tableName': " . $e->getMessage() . "\n";
        return false;
    }
}

	
}

?>