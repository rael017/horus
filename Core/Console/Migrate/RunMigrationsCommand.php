<?php
namespace Core\Console\Migrate;
use Core\DataBase\MigrationManeger;


class RunMigrationsCommand
{
    public static function execute(array $options): void
    {
        $pendingMigrations = self::getPendingMigrations();

        if (empty($pendingMigrations)) {
            echo "Nenhuma migração nova para executar.\n";
            return;
        }

        $batch = MigrationManeger::getLastBatchNumber() + 1;
        echo "Iniciando migrações (Lote: $batch)...\n";

        foreach ($pendingMigrations as $file) {
            $migrationName = pathinfo($file, PATHINFO_FILENAME);
            
            echo "Migrando: {$migrationName}\n";
            
            require_once $file;
            
            $className = self::convertFileNameToClassName($migrationName);
            
            $fullClassName = "Core\\DataBase\\Migrations\\$className";


            if (!class_exists($fullClassName)) {
                echo "Classe $fullClassName não encontrada para o arquivo $fileName\n";
                continue; // Pula para o próximo arquivo de migração
            }

           

            try {
                $migrationInstance = new $fullClassName();
                $migrationInstance->up(); // Executa o método UP
                MigrationManeger::logMigration($migrationName,$className, $batch); // Registra no log
                echo "Migrado:  {$migrationName}\n";
                
                $tableName = 'tb_'. strtolower($className); // Ex: 'users'
                $modelName = str_replace('tb_', '',$className); // Ex: 'Users'
                
                self::createModelFromMigration($tableName, $modelName);
            
            } catch (\Exception $e) {
                echo "  -> ERRO DURANTE A MIGRAÇÃO: " . $e->getMessage() . "\n";
                // Interrompe o processo em caso de erro para evitar inconsistências.
                return;
            }
        }

        echo "Migrações concluídas com sucesso.\n";
    
    }

    protected static function createModelFromMigration($tableName, $modelName)
    {
        echo "Criando modelo para a tabela: $tableName\n";

        $fields = \Core\DataBase\Crud::Describe($tableName);
        if (!$fields) {
            echo "Não foi possível descrever os campos da tabela '$tableName'.\n";
            return;
        }

        $filePath = "App/Models/{$modelName}.php";
        echo "Caminho do arquivo de modelo: $filePath\n";

        // Criar propriedades a partir dos campos da tabela
        $properties = [];
        $createFields = [];
        $updateFields = [];
        foreach ($fields as $field) {
            $fieldName = $field['Field'];
            $properties[] = "public \${$fieldName};";
            if ($fieldName !== 'id' && $fieldName !== 'created_at') { // Ignorar o ID no CRUD de criação/atualização
                $createFields[] = "'$fieldName' => \$this->{$fieldName}";
                $updateFields[] = "'$fieldName' => \$this->{$fieldName}";
            }
        }

        $stub = file_get_contents(__DIR__ . '/../../../Stubs/model.stub');
        echo "Conteúdo do stub carregado.\n";

        // Substituir no stub os placeholders pelos valores dinâmicos
        $model = str_replace(
            ['{{ className }}', '{{ tableName }}', '{{ properties }}', '{{ createFields }}', '{{ updateFields }}'],
            [
                $modelName,
                $tableName,
                implode("\n    ", $properties),
                implode(",\n            ", $createFields),
                implode(",\n            ", $updateFields),
            ],
            $stub
        );

        file_put_contents($filePath, $model);
        echo "Model '{$modelName}' criado em '{$filePath}'.\n";
    }


    private static function getPendingMigrations(): array
    {
        $migrationsPath = __DIR__ . '/../../DataBase/Migrations';
        if (!is_dir($migrationsPath)) {
            return [];
        }
        
        $allFiles = glob("{$migrationsPath}/*.php");
        $ranMigrations = MigrationManeger::getRanMigrations();

        return array_filter($allFiles, function ($file) use ($ranMigrations) {
            $migrationName = pathinfo($file, PATHINFO_FILENAME);
            return !in_array($migrationName, $ranMigrations);
        });
    }

    private static function convertFileNameToClassName(string $fileName): string
    {
         echo "Convertendo nome do arquivo $fileName para nome de classe.\n";
        
        // Remove o prefixo de data e horário do nome do arquivo
        $fileName = preg_replace('/\d/', '', $fileName); // Remove o timestamp do início

        // Remove prefixos e sufixos relacionados à migração
    
        // Converte o nome do arquivo para formato CamelCase
        $fileName = str_replace('_', ' ', $fileName); // Substitui underscores por espaços
        $fileName = str_replace(' ', '', $fileName); // Remove os espaços para criar CamelCase
        return $fileName;
    }
}
