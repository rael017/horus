<?php
namespace App\Models;

use Core\DataBase\Crud;

class User
{
    public $id;
    public $created_at;
    public $updated_at;
    public $name;

    protected $tableName = 'tb_user';

    public function create()
    {
        $this->id = Crud::Create($this->tableName, [
            'updated_at' => $this->updated_at,
            'name' => $this->name
        ]);
        return true;
    }

    public function update()
    {
        return Crud::Update($this->tableName, "id = {$this->id}", [
            'updated_at' => $this->updated_at,
            'name' => $this->name
        ]);
    }

    public function delete()
    {
        return Crud::Delete($this->tableName, "id = {$this->id}");
    }
}
