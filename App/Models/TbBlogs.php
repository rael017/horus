<?php
namespace App\Models;

use Core\DataBase\Crud;

class TbBlogs
{
    public $id;
    public $name;
    public $email;
    public $created_at;

    protected $tableName = 'tb_blogs';

    public function create()
    {
        $this->id = Crud::Create($this->tableName, [
            'name' => $this->name,
            'email' => $this->email
        ]);
        return true;
    }

    public function update()
    {
        return Crud::Update($this->tableName, "id = {$this->id}", [
            'name' => $this->name,
            'email' => $this->email
        ]);
    }

    public function delete()
    {
        return Crud::Delete($this->tableName, "id = {$this->id}");
    }
}
