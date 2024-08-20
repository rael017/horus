<?php

namespace App\Console\Commands;

class CreateSeederCommand
{
    public static function execute($name)
    {
        if (!$name) {
            die("Nome do seeder é necessário.\n");
        }

        $className = $name . 'TableSeeder';
        $filePath = __DIR__ . "/../../DataBase/Seeders/{$className}.php";
        $stub = file_get_contents(__DIR__ . '/../../../Stubs/seeder.stub');

        $seeder = str_replace(
            ['{{ className }}', '{{ tableName }}'],
            [$className, $name],
            $stub
        );

        file_put_contents($filePath, $seeder);
        echo "Seeder {$className} criado em {$filePath}.\n";
    }
}

?>
