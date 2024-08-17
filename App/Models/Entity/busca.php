<?php

namespace App\Models\Entity;
use \PDO;
use \App\Models\DataBase\Crud;
class Busca
{
	public $id;
	public $categoria;
	public $autor;
	public $titulo;
	public $post;
	public $data;
	public $slug;

	public static function getPost($where = null, $order = null, $limit = null, $filds = '*'){
		return Crud::Ready('tb_lenon_blog',$where,$order,$limit,$filds)->fetchAll(self::class);
	}
	
}

?>