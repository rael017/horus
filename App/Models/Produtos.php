<?php
namespace App\Models;

use Core\DataBase\Crud;

class Produtos
{
    public $id;
    public $name;
    public $description;
    public $price;
    public $stock;

    protected $tableName = 'tb_produtos';

    public function create()
    {
        $this->id = Crud::Create($this->tableName, [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock
        ]);
        return true;
    }

    public function update()
    {
        return Crud::Update($this->tableName, "id = {$this->id}", [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock
        ]);
    }

    public function delete()
    {
        return Crud::Delete($this->tableName, "id = {$this->id}");
    }
}
