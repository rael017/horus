<?php
namespace App\Models;

use Core\DataBase\Crud;

class Users
{
    public $id;
    public $name;
    public $email;
    public $password;

    protected $tableName = 'tb_users';

    public function create()
    {
        $this->id = Crud::Create($this->tableName, [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password
        ]);
        return true;
    }

    public function update()
    {
        return Crud::Update($this->tableName, "id = {$this->id}", [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password
        ]);
    }

    public function delete()
    {
        return Crud::Delete($this->tableName, "id = {$this->id}");
    }
}
