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

	
    
    // ... outros métodos ...

    public static function Describe($table)
    {
        try {
            $query = parent::execute("DESCRIBE `$table`");
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if ($e->getCode() == '42S02') { // Tabela não encontrada
                return []; // Retorna array vazio em vez de false para consistência
            }
            throw $e; // Relança outros erros
        }
    }

    /**
     * Verifica se uma tabela existe no banco de dados.
     * CORRIGIDO: Usa a constante DB_NAME para ser dinâmico.
     */
    public static function tableExists(string $tableName): bool
    {
        try {
            $sql = "SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? LIMIT 1";
            $stmt = parent::execute($sql, [DB_NAME, $tableName]);
            return $stmt->fetchColumn() !== false;
        } catch (PDOException $e) {
            error_log("Erro ao verificar existência da tabela '$tableName': " . $e->getMessage());
            return false;
        }
    }

	
}

?>