<?php
namespace Core\Console;


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
                \Core\Console\Migrate\RunMigrationsCommand::execute($modelFlag, $modelName);
                break;
            case 'seeder':
                \Core\Console\Seeders\RunSeedersCommand::execute();
                break;
            case 'make':
                if (isset($args[2])) {
                    switch ($args[2]) {
                        case 'migrate':
                            \Core\Console\Migrate\CreateMigrationCommand::execute($args[3] ?? null);
                            break;
                        case 'seeder':
                            \Core\Console\Seeders\CreateSeederCommand::execute($args[3] ?? null);
                            break;
                    }
                }
                break;

            case 'server':
                

            default:
                echo "Comando não reconhecido: $command\n";
                break;
        }
    }
}

?>
