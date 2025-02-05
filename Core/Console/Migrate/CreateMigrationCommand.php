<?php
namespace Core\Console\Migrate;
use \Core\DataBase\Crud;

class CreateMigrationCommand
{
    public static function execute($name)
    {
        if (!$name) {
            die("Nome da migration é necessário.\n");
        }

        $timestamp = date('Y_m_d_His');
        $className = $timestamp . '_Create_Tb_' . ucfirst($name) . '_Table';
        $filePath = __DIR__ . "/../../DataBase/Migrations/{$className}.php";
        $stub = file_get_contents(__DIR__ . '/../../../Stubs/migration.stub');

        $migration = str_replace(
            ['{{ className }}', '{{ tableName }}'],
            [ucfirst($name), $name],
            $stub
        );

        file_put_contents($filePath, $migration);
        self::registerMigrationInDatabase($className, $name);

        echo "Migration {$className} criada em {$filePath}.\n";
    }

    private static function registerMigrationInDatabase($className, $tableName)
    {
        // Verifica se a tabela de controle existe
        if (!Crud::tableExists('migrations')) {
            echo "Criando tabela de controle 'migrations'...\n";
            \Core\DataBase\MigrationManeger::createTable('migrations',[
                
                'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
                'nome' => 'VARCHAR(100) NOT NULL',
                'nome_tabela' => 'VARCHAR(100) NOT NULL',
                'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        
            ]);
            echo "Tabela de controle 'migrations' criada com sucesso.\n";
        }

        // Adiciona a nova migração na tabela
        Crud::Create('migrations', [
            'nome'=> $tableName,
            'nome_tabela'=>'tb_'.$tableName
        ]);
    }
}



?>
