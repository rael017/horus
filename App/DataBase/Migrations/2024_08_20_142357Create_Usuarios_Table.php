<?php

use App\DataBase\Crud;
use App\DataBase\Migration;
Class usuarios extends Migration
{
    public function up()
    {
        
        Crud::createTable('usuarios',
        [
            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'name' => 'VARCHAR(100) NOT NULL',
            'email' => 'VARCHAR(100) NOT NULL',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ]);
    }

    public function down()
    {
        Crud::dropTable('usuarios');
    }
}

?>
