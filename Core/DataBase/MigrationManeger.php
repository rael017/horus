<?php

namespace Core\DataBase;
use PDO;

class MigrationManeger extends Conection
{
    /**
     * Garante que a tabela de controle de migrações exista e tenha a estrutura correta.
     */
    public static function ensureMigrationsTableExists(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS `migrations` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `migration` VARCHAR(255) NOT NULL UNIQUE,
            `nome_tb` VARCHAR(255) NOT NULL UNIQUE,
            `batch` INT NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        parent::execute($sql);
    }

    /**
     * Retorna um array com os nomes de todas as migrações que já foram executadas.
     */
    public static function getRanMigrations(): array
    {
        $results = parent::execute("SELECT `migration` FROM `migrations`")->fetchAll(PDO::FETCH_COLUMN);
        return $results ?: [];
    }

    /**
     * Retorna o número do último lote executado.
     */
    public static function getLastBatchNumber(): int
    {
        $result = parent::execute("SELECT MAX(`batch`) as `max_batch` FROM `migrations`")->fetch();
        return $result['max_batch'] ?? 0;
    }

    /**
     * Registra uma migração executada no banco de dados.
     */
    public static function logMigration(string $migrationName, $className , int $batch): void
    {
        ; // Remove os espaços para criar CamelCase
        parent::execute(
            "INSERT INTO `migrations` (`migration`, `nome_tb`,`batch`) VALUES (?, ?, ?)",
            [$migrationName,$className, $batch]
        );
    }

    /**
     * Retorna todas as migrações do último lote para o rollback.
     */
    public static function getMigrationsForLastBatch(): array
    {
        $lastBatch = self::getLastBatchNumber();
        if ($lastBatch === 0) {
            return [];
        }
        return parent::execute("SELECT * FROM `migrations` WHERE `batch` = ? ORDER BY `id` DESC", [$lastBatch])->fetchAll();
    }

    /**
     * Remove o registro de uma migração do log (usado no rollback).
     */
    public static function removeMigrationFromLog(int $migrationId): void
    {
        parent::execute("DELETE FROM `migrations` WHERE `id` = ?", [$migrationId]);
    }
    
    // --- Métodos de Schema (usados pelos arquivos de migração) ---

    public static function createTable(string $tableName, array $columns): void
    {
        $columnDefinitions = [];
        foreach ($columns as $column => $definition) {
            $columnDefinitions[] = "`$column` $definition";
        }
        $columnsSql = implode(', ', $columnDefinitions);
        $sql = "CREATE TABLE `$tableName` ($columnsSql) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        parent::execute($sql);
    }

    public static function dropTable(string $tableName): void
    {
        parent::execute("DROP TABLE IF EXISTS `$tableName`");
    }
}