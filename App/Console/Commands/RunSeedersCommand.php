<?php

namespace App\Console\Commands;

use App\DataBase\Crud;

class RunSeedersCommand
{
    public static function execute()
    {
        $files = glob(__DIR__ . '/../../DataBase/Seeders/*.php');

        foreach ($files as $file) {
            require_once $file;
            $className = basename($file, '.php');
            $migration = new $className();
            $migration->up();
        }

        echo "Migrations executadas com sucesso.\n";
    }
}

?>
