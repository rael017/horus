<?php
namespace Core\Console\Migrate;
use \Core\DataBase\Crud;

class CreateMigrationCommand
{

    public static function execute(?string $name): void
    {
        if (empty($name)) {
            echo "Erro: Nome da migração é necessário.\nExemplo: php horus make:migrate create_users_table\n";
            return;
        }

        
        $timestamp = date('Y_m_d_His');
        $fileName = $timestamp.'_'.ucfirst($name);
        $className = ucfirst($name);
        
        $migrationsDir = __DIR__ . '/../../DataBase/Migrations';
        if (!is_dir($migrationsDir)) {
            mkdir($migrationsDir, 0755, true);
        }
        $filePath = "{$migrationsDir}/{$fileName}.php";

        $stubPath = __DIR__ . '/../../../Stubs/migration.stub';
        if (!file_exists($stubPath)) {
            echo "Erro: Arquivo de template 'Stubs/migration.stub' não encontrado.\n";
            return;
        }
        $stub = file_get_contents($stubPath);

        // NOVO: Gera as colunas com base no nome da tabela
        $columns = self::generateSchemaForTable($name);

        // Substitui os placeholders, incluindo as novas colunas
        $migrationContent = str_replace(
            ['{{className}}', '{{tableName}}', '{{columns}}'],
            [$className, 'tb_' . $name, $columns],
            $stub
        );

        file_put_contents($filePath, $migrationContent);

        echo "Migration criada com o esquema para '$name': {$fileName}.php\n";
    }

   /* public static function execute($name)
    {
        if (!$name) {
            die("Nome da migration é necessário.\n");
        }

        $timestamp = date('Y_m_d_His');
        $className = $timestamp . '_'.ucfirst($name) ;
        $filePath = __DIR__ . "/../../DataBase/Migrations/{$className}.php";
        $stub = file_get_contents(__DIR__ . '/../../../Stubs/migration.stub');

        $columns = self::generateSchemaForTable($name);

        $migration = str_replace(
            ['{{ className }}', '{{ tableName }}, {{columns}}'],
            [ucfirst($name), $name, $colums],
            $stub
        );

        file_put_contents($filePath, $migration);

        echo "Migration {$className} criada em {$filePath}.\n";
    }
    */
    
    private static function generateSchemaForTable(string $tableName): string
    {
        $columns = [];
        
        // Esquema Padrão para todas as tabelas
        $baseSchema = [
            "'id'         => 'INT AUTO_INCREMENT PRIMARY KEY'",
        ];
        
        // Esquemas Específicos
        switch (true) {
            case str_contains($tableName, 'users'):
            case str_contains($tableName, 'usuarios'):
                $columns = [
                    "'name'       => 'VARCHAR(255) NOT NULL'",
                    "'email'      => 'VARCHAR(255) NOT NULL UNIQUE'",
                    "'password'   => 'VARCHAR(255) NOT NULL'",
                ];
                break;
                
            case str_contains($tableName, 'products'):
            case str_contains($tableName, 'produtos'):
                $columns = [
                    "'name'        => 'VARCHAR(255) NOT NULL'",
                    "'description' => 'TEXT NULL'",
                    "'price'       => 'DECIMAL(10, 2) NOT NULL DEFAULT 0.00'",
                    "'stock'       => 'INT NOT NULL DEFAULT 0'",
                ];
                break;

            case str_contains($tableName, 'blog'):
            case str_contains($tableName, 'posts'):
                 $columns = [
                    "'title'       => 'VARCHAR(255) NOT NULL'",
                    "'content'     => 'TEXT NOT NULL'",
                    "'author_id'   => 'INT NOT NULL'",
                ];
                break;
            
            // Adicione outros casos aqui...
            // case str_contains($tableName, 'orders'):
            // ...

            default:
                // Se nenhum esquema corresponder, usa um esquema genérico.
                $columns = [
                    "'id' => 'INT AUTO_INCREMENT PRIMARY KEY'",
                    "'name' => 'VARCHAR(255) NOT NULL'"
                ];
                break;
        }

        // Junta o esquema específico com o esquema base e formata a string
        $fullSchema = array_merge($baseSchema,$columns);
        return implode(",\n            ", $fullSchema);
    }

}



?>
