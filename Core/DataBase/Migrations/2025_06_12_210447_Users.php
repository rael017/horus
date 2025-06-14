<?php
namespace Core\DataBase\Migrations;
use Core\DataBase\MigrationManeger;

Class Users
{

    /**
    * Executa a migração.
    */
    
    public function up(): void
    {
        MigrationManeger::createTable('tb_users', [
            'id'         => 'INT AUTO_INCREMENT PRIMARY KEY',
            'name'       => 'VARCHAR(255) NOT NULL',
            'email'      => 'VARCHAR(255) NOT NULL UNIQUE',
            'password'   => 'VARCHAR(255) NOT NULL'
        ]);
    }

    /**
    * Reverte a migração.
    */

    public function down(): void
    {
        MigrationManeger::dropTable('tb_users');
    }
}

?>
