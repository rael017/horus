<?php

namespace Core\Console\Seeders;

class CreateSeederCommand
{
    public static function execute($name)
    {
        if (!$name) {
            die("Nome do seeder é necessário.\n");
        }

        $className = $name . 'Table';
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
