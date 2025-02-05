<?php

namespace Core\DataBase;

class MigrationManeger extends Conection
{
    // Método para criar tabelas
    public static function createTable($tableName, $columns) {
        $columnDefinitions = [];
        foreach ($columns as $column => $definition) {
            $columnDefinitions[] = "`$column` $definition";
        }
        $columnsSql = implode(', ', $columnDefinitions);

        $sql = "CREATE TABLE `$tableName` ($columnsSql)";
        
        parent::execute($sql);
        return true;
    }

    // Método para apagar tabelas
    public static function dropTable($tableName) {
        $sql = "DROP TABLE IF EXISTS `$tableName`";
        
        parent::execute($sql);
        return true;
    }

    // Método para realizar rollback por lote
    public static function rollbackByBatch($batchNumber) {
        $migrations = self::getMigrationsByBatch($batchNumber);
        foreach ($migrations as $migration) {
            self::createBackup($migration['table_name']); // Cria um backup da tabela antes do rollback
            $migrationInstance = new $migration['class_name']();
            $migrationInstance->down();  // Reverter migração
            self::removeMigrationFromLog($migration['id']);
        }
    }

    // Método para realizar rollback por nome
    public static function rollbackByName($migrationName) {
        $migration = self::getMigrationByName($migrationName);
        if ($migration) {
            self::createBackup($migration['table_name']); // Cria um backup da tabela antes do rollback
            $migrationInstance = new $migration['class_name']();
            $migrationInstance->down();  // Reverter migração
            self::removeMigrationFromLog($migration['id']);
        } else {
            echo "Migração não encontrada: $migrationName\n";
        }
    }

    // Método para realizar rollback de todas as migrações
    public static function rollbackAll() {
        $migrations = self::getAllMigrations();
        foreach ($migrations as $migration) {
            self::createBackup($migration['table_name']); // Cria um backup da tabela antes do rollback
            $migrationInstance = new $migration['class_name']();
            $migrationInstance->down();  // Reverter migração
            self::removeMigrationFromLog($migration['id']);
        }
    }

    // Método para realizar rollback da última migração
    public static function rollbackLastMigration() {
        $lastMigration = self::getLastMigration();
        if ($lastMigration) {
            self::createBackup($lastMigration['table_name']); // Cria um backup da tabela antes do rollback
            $migrationInstance = new $lastMigration['class_name']();
            $migrationInstance->down();  // Reverter migração
            self::removeMigrationFromLog($lastMigration['id']);
        } else {
            echo "Nenhuma migração encontrada para reverter.\n";
        }
    }

    // Método para criar backup de uma tabela
    private static function createBackup($tableName) {
        $backupTable = "backup_" . $tableName . "_" . date("Y_m_d_H_i_s");
        $sql = "CREATE TABLE `$backupTable` AS SELECT * FROM `$tableName`";
        
        parent::execute($sql);
    }

    // Método para obter as migrações por lote
    private static function getMigrationsByBatch($batchNumber) {
        // Exemplo simplificado: substitua pelo código que obtém as migrations do banco de dados ou de um arquivo de log
        return self::query("SELECT * FROM migrations WHERE batch = ?", [$batchNumber]);
    }

    // Método para obter a migração pelo nome
    private static function getMigrationByName($migrationName) {
        // Exemplo simplificado: substitua pelo código que obtém a migração do banco de dados ou de um arquivo de log
        return self::query("SELECT * FROM migrations WHERE name = ?", [$migrationName]);
    }

    // Método para obter todas as migrações
    private static function getAllMigrations() {
        // Exemplo simplificado: substitua pelo código que obtém todas as migrations do banco de dados ou de um arquivo de log
        return self::query("SELECT * FROM migrations");
    }

    // Método para obter a última migração
    private static function getLastMigration() {
        // Exemplo simplificado: substitua pelo código que obtém a última migração do banco de dados ou de um arquivo de log
        return self::query("SELECT * FROM migrations ORDER BY id DESC LIMIT 1");
    }

    // Método para remover a migração do log após o rollback
    private static function removeMigrationFromLog($migrationId) {
        // Exemplo simplificado: substitua pelo código que remove a migração do banco de dados ou de um arquivo de log
        parent::execute("DELETE FROM migrations WHERE id = ?", [$migrationId]);
    }

    // Método para executar consultas ao banco de dados
    private static function query($sql, $params = []) {
        // Exemplo simplificado: substitua pela lógica de execução de consultas ao banco de dados
        $stmt = parent::execute($sql,$params);
        return $stmt->fetchAll();
    }
}
