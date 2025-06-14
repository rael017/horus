<?php
namespace Core\DataBase;

use \PDO;
use \PDOException;

/**
 * Classe de Conexão Resiliente
 * Esta versão é otimizada para ambientes de longa duração como o Horus Nexus.
 * Ela verifica se a conexão ainda está ativa antes de cada consulta e se reconecta
 * se necessário, prevenindo o erro "MySQL server has gone away".
 */


/**
 * Classe de Conexão Resiliente - Versão 3 (Otimizada para Nexus)
 * * Esta versão adota a estratégia mais robusta para ambientes de longa duração:
 * cria uma nova conexão para cada pedido quando a aplicação está a correr no modo Nexus,
 * eliminando completamente os erros de "MySQL server has gone away".
 * Para comandos CLI normais, mantém o comportamento eficiente de uma única conexão.
 */
class Conection
{
    /** @var PDO|null A instância estática do PDO, usada APENAS em ambientes não-Nexus. */
    private static ?PDO $pdo = null;

    /**
     * Obtém uma instância de conexão PDO.
     */
    protected static function conectar(): PDO
    {
        // Se estamos a correr no modo Nexus, NUNCA reutilize a conexão. Crie sempre uma nova.
        // Isto é rápido e garante que cada pedido tem uma ligação fresca e válida.
        if (defined('HORUS_NEXUS_MODE') && HORUS_NEXUS_MODE === true) {
            return self::createNewConnection();
        }

        // Para ambientes CLI (como migrações), reutilize a conexão para eficiência.
        if (self::$pdo === null) {
            self::$pdo = self::createNewConnection();
        }
        
        return self::$pdo;
    }

    /**
     * Lógica centralizada para criar uma nova conexão PDO.
     */
    private static function createNewConnection(): PDO
    {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                PDO::ATTR_TIMEOUT            => 5, // Timeout da ligação em segundos
            ];
            return new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Erro de conexão com o banco de dados: " . $e->getMessage());
            // Lança a exceção para que possa ser apanhada pelo Guardian ou pelo manipulador de erros.
            throw $e;
        }
    }

    /**
     * Prepara e executa uma instrução SQL.
     */
    protected static function execute(string $sql, array $params = []): \PDOStatement
    {
        try {
            // Obtém uma conexão (nova ou reutilizada, dependendo do ambiente)
            $connection = self::conectar(); 
            $statement = $connection->prepare($sql);
            $statement->execute($params);
            return $statement;
        } catch (PDOException $e) {
            error_log("Erro ao executar SQL: " . $e->getMessage() . " | SQL: " . $sql);
            throw new PDOException("Erro ao executar a consulta: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }
}