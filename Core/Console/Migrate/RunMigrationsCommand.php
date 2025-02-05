<?php
namespace Core\Console\Migrate;

use Core\DataBase\Crud;

class RunMigrationsCommand
{
    public static function execute($createModel = false, $modelName = null)
    {
        echo "Iniciando execução de migrações...\n";

        // Obter todos os arquivos de migração
        $migrationFiles = glob(__DIR__ . '/../../DataBase/Migrations/*.php');


        foreach ($migrationFiles as $migrationFile) {
            require_once $migrationFile;
            $fileName = basename($migrationFile);
            $className = self::convertFileNameToClassName($fileName);

            $fullClassName = "Core\\DataBase\\Migrations\\$className";


            if (!class_exists($fullClassName)) {
                echo "Classe $fullClassName não encontrada para o arquivo $fileName\n";
                continue; // Pula para o próximo arquivo de migração
            }

            echo "Executando a migração: $className\n";
            $migration = new $fullClassName();

            // Verificar se a tabela já existe
            try {
                $tableName = $migration->getTableName();
                if (Crud::tableExists($tableName)) {
                    echo "Tabela '$tableName' já existe. Pulando migração.\n";
                    continue;
                }

                // Execute a migração aqui
            } catch (\Exception $e) {
                echo "Erro durante a migração: " . $e->getMessage() . "\n";
            }

            // Executar a migração
            $migration->up();
            echo "Migration $className executada.\n";

            $modelNameUse = $modelName ?? $className;

            if ($createModel) {
                echo "Iniciando criação do modelo a partir da migração.\n";
                self::createModelFromMigration($tableName, $modelNameUse);
            }
        }

        echo "Execução de migrações concluída.\n";
    }

    protected static function convertFileNameToClassName($fileName)
    {
         echo "Convertendo nome do arquivo $fileName para nome de classe.\n";
        
        // Remove o prefixo de data e horário do nome do arquivo
        $fileName = preg_replace('/\d/', '', $fileName); // Remove o timestamp do início

        // Remove prefixos e sufixos relacionados à migração
        $fileName = str_replace(['Create', 'Table', '_', '.php','Tb'], '', $fileName);
        $fileName = str_replace('.php', '', $fileName);

        // Converte o nome do arquivo para formato CamelCase
        $fileName = str_replace('_', ' ', $fileName); // Substitui underscores por espaços
        $fileName = str_replace(' ', '', $fileName); // Remove os espaços para criar CamelCase
        return $fileName;
    }

   protected static function createModelFromMigration($tableName, $modelName)
    {
        echo "Criando modelo para a tabela: $tableName\n";

        $fields = Crud::Describe($tableName);
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


    protected static function tableExists($tableName)
    {
        try {
            $result = Crud::Describe($tableName);
            return !empty($result);
        } catch (\Exception $e) {
            return false;
        }
    }
}
?>
z
