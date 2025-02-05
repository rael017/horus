<?php
namespace Core\DataBase\Migrations;
use Core\DataBase\MigrationManeger;

Class Posts
{
    public function getTableName()
    {
        return 'tb_posts';
    }

    public function up()
    {
        
        MigrationManeger::createTable('tb_posts',
        [
            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'name' => 'VARCHAR(100) NOT NULL',
            'email' => 'VARCHAR(100) NOT NULL',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ]);
    }

    public function down()
    {
        MigrationManeger::dropTable('tb_posts');
    }
}

?>
