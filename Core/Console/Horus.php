<?php
namespace Core\Console;

class Horus
{
    public static function run($args)
    {
        // Define a constante da raiz do projeto, agora a partir da perspetiva do contentor
       
        $command = $args[1] ?? null;

        // --- LÓGICA DE ANÁLISE DE ARGUMENTOS MELHORADA ---
        $mainArgument = null;
        $options = [];

        // Itera sobre todos os argumentos após o nome do comando
        for ($i = 2; $i < count($args); $i++) {
            // Se o argumento começa com '-', é uma opção (flag)
            if (str_starts_with($args[$i], '-')) {
                $options[] = $args[$i];
            } 
            // O primeiro argumento que não é uma opção é considerado o argumento principal
            else if ($mainArgument === null) {
                $mainArgument = $args[$i];
            }
        }

        switch ($command) {
            
            
            case 'run:migrate':
                 \Core\DataBase\MigrationManeger::ensureMigrationsTableExists();
                 \Core\Console\Migrate\RunMigrationsCommand::execute($options);
                 break;
            
            // ... adicione outros comandos aqui ...
            case 'make:migrate':
                // Agora passa o argumento principal correto
                \Core\Console\Migrate\CreateMigrationCommand::execute($mainArgument);
                break;
            
            case 'make:seeder':
                \Core\Console\Seeders\CreateSeederCommand::execute($mainArgument);
                break;
            
            case 'run:seeder':
                \Core\Console\Seeders\RunSeedersCommand::execute();
                break;
            
            case 'rollback':
                \Core\Console\Migrate\RollbackCommand::execute();
                break;

            default:
                echo "Comando não reconhecido: '$command'\n";
                break;
        }
    }
}