<?php
namespace Core\Console\Migrate;

use Core\DataBase\MigrationManeger;

class RollbackCommand
{
    public static function execute(): void
    {
        $migrationsToRollback = MigrationManeger::getMigrationsForLastBatch();

        if (empty($migrationsToRollback)) {
            echo "Nenhuma migração para reverter.\n";
            return;
        }

        echo "Revertendo último lote de migrações...\n";

        foreach ($migrationsToRollback as $migration) {
            $migrationName = $migration['migration'];
            $migrationFile = __DIR__ . "/../../DataBase/Migrations/{$migrationName}.php";

            echo "Revertendo: {$migrationName}\n";

            if (!file_exists($migrationFile)) {
                echo "  -> Erro: Arquivo '{$migrationName}.php' não encontrado.\n";
                continue;
            }
            
            require_once $migrationFile;
            $className = self::convertFileNameToClassName($migrationName);

            $fullClassName = "Core\\DataBase\\Migrations\\$className";
            if (!class_exists($fullClassName)) {
                 echo "  -> Erro: Classe '$fullClassName' não encontrada.\n";
                continue;
            }
            
            try {
                $migrationInstance = new $fullClassName();
                $migrationInstance->down(); // Executa o método DOWN
                MigrationManeger::removeMigrationFromLog($migration['id']); // Remove do log
                echo "Revertido:  {$migrationName}\n";
            } catch (\Exception $e) {
                echo "  -> ERRO DURANTE O ROLLBACK: " . $e->getMessage() . "\n";
                return; // Interrompe para evitar inconsistências
            }
        }
        
        echo "Rollback concluído com sucesso.\n";
    }
    
    private static function convertFileNameToClassName(string $fileName): string
    {
       $fileName = preg_replace('/\d/', '', $fileName); // Remove o timestamp do início

        // Remove prefixos e sufixos relacionados à migração
    
        // Converte o nome do arquivo para formato CamelCase
        $fileName = str_replace('_', ' ', $fileName); // Substitui underscores por espaços
        $fileName = str_replace(' ', '', $fileName); // Remove os espaços para criar CamelCase
        return $fileName;
    }
    
}