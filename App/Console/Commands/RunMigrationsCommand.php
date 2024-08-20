<?php

namespace App\Console\Commands;

use ReflectionClass;

class RunMigrationsCommand
{
    public static function execute($createModel = false, $modelName = null)
    {
        $migrationFiles = glob(__DIR__ . '/../../DataBase/Migrations/*.php');

        foreach ($migrationFiles as $migrationFile) {
            require_once $migrationFile;
            $fileName = basename($migrationFile, '.php');
            $className = self::convertFileNameToClassName($fileName);

            if (!class_exists($className)) {
                echo "Classe $className não encontrada para o arquivo $fileName.\n";
                echo "DB_HOST: " . getenv('DB_HOST') . "\n";
echo "DB_NAME: " . getenv('DB_NAME') . "\n";
echo "DB_USER: " . getenv('DB_USER') . "\n";
echo "DB_PASS: " . getenv('DB_PASS') . "\n";
exit;
            }

            $migration = new $className();
            $migration->up();

            echo "Migration $className executada.\n";

            if ($createModel && $modelName) {
                self::createModelFromMigration($migrationFile, $modelName);
            }
        }
    }

        protected static function convertFileNameToClassName($fileName)
        {
            // Remove a extensão .php do nome do arquivo
            $fileName = pathinfo($fileName, PATHINFO_FILENAME);

            // Remove o prefixo de data e horário do nome do arquivo
            $fileName = preg_replace('/\d/', '', $fileName); // Remove o timestamp do início

            // Remove prefixos e sufixos relacionados à migração
            $fileName = str_replace(['Create_', '_Table'], '', $fileName);

            // Converte o nome do arquivo para formato CamelCase
            $fileName = str_replace('_', ' ', $fileName); // Substitui underscores por espaços
            $fileName = ucwords($fileName); // Capitaliza a primeira letra de cada palavra
            $fileName = str_replace(' ', '', $fileName); // Remove os espaços para criar CamelCase

            return $fileName;
        }




    protected static function createModelFromMigration($migrationFile, $modelName)
    {
        $tableName = strtolower(str_replace('Create', '', str_replace('Table', '', basename($migrationFile, '.php'))));

        $fields = self::extractFieldsFromMigration($migrationFile);

        if (!$fields) {
            echo "Não foi possível extrair os campos da migration $modelName.\n";
            return;
        }

        $filePath = __DIR__ . "/../../App/Models/{$modelName}.php";
        $stub = file_get_contents(__DIR__ . '/../../../stubs/model.stub');

        $model = str_replace(
            ['{{ className }}', '{{ tableName }}', '{{ fields }}'],
            [$modelName, $tableName, implode(", ", $fields)],
            $stub
        );

        file_put_contents($filePath, $model);
        echo "Model {$modelName} criado em {$filePath}.\n";
    }

    protected static function extractFieldsFromMigration($migrationFile)
    {
        $fields = [];
        $migrationContent = file_get_contents($migrationFile);

        preg_match_all('/\$table->(.*?);/', $migrationContent, $matches);

        foreach ($matches[1] as $fieldDefinition) {
            $fieldParts = explode("'", $fieldDefinition);
            if (isset($fieldParts[1])) {
                $fields[] = "'".$fieldParts[1]."'";
            }
        }

        return $fields;
    }
}
