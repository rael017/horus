<?php

namespace App\Console;

class Horus
{
    public static function run($args)
    {
        if (count($args) < 2) {
            die("Comando inválido. Use: php Horus [comando]\n");
        }

        $command = $args[1];

        switch ($command) {
            case 'migrate':
                $modelFlag = in_array('-m', $args);
                $modelName = $modelFlag ? ($args[array_search('-m', $args) + 1] ?? null) : null;
                \App\Console\Commands\RunMigrationsCommand::execute($modelFlag, $modelName);
                break;
            case 'seed':
                \App\Console\Commands\RunSeedersCommand::execute();
                break;
            case 'New':
                if (isset($args[2])) {
                    switch ($args[2]) {
                        case 'migrate':
                            \App\Console\Commands\CreateMigrationCommand::execute($args[3] ?? null);
                            break;
                        case 'seeder':
                            \App\Console\Commands\CreateSeederCommand::execute($args[3] ?? null);
                            break;
                    }
                }
                break;
            default:
                echo "Comando não reconhecido: $command\n";
                break;
        }
    }
}

?>
