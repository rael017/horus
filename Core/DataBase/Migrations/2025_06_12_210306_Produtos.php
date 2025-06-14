<?php
namespace Core\DataBase\Migrations;
use Core\DataBase\MigrationManeger;

Class Produtos
{

    /**
    * Executa a migração.
    */
    
    public function up(): void
    {
        MigrationManeger::createTable('tb_produtos', [
            'id'         => 'INT AUTO_INCREMENT PRIMARY KEY',
            'name'        => 'VARCHAR(255) NOT NULL',
            'description' => 'TEXT NULL',
            'price'       => 'DECIMAL(10, 2) NOT NULL DEFAULT 0.00',
            'stock'       => 'INT NOT NULL DEFAULT 0'
        ]);
    }

    /**
    * Reverte a migração.
    */

    public function down(): void
    {
        MigrationManeger::dropTable('tb_produtos');
    }
}

?>
