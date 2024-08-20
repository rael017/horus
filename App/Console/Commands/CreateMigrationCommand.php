<?php

namespace App\Console\Commands;

class CreateMigrationCommand
{
    public static function execute($name)
    {
        if (!$name) {
            die("Nome da migration é necessário.\n");
        }

        $timestamp = date('Y_m_d_His');
        $className = $timestamp . 'Create_' . ucfirst($name) . '_Table';
        
        $filePath = __DIR__ . '/../../DataBase/Migrations/{$className}.php';
        $stub = file_get_contents(__DIR__ . '/../../../Stubs/migration.stub');

        $migration = str_replace(
            ['{{ className }}', '{{ tableName }}'],
            [$className, $name],
            $stub
        );

        file_put_contents($filePath, $migration);
        echo "Migration {$className} criada em {$filePath}.\n";
    }
}

?>
