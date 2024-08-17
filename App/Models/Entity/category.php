<?php

namespace App\Models\Entity;
use \App\Models\DataBase\Crud;
use \App\Utils\Slug;

use \App;
class category
{
	public $id;
	public $nome;
	public $slug;
	
	
	
	public function cadastrar()
	{
		
		$this->id = Crud::Create('tb_blog_categoria',[
			'nome'  =>$this->nome,
			'slug'  =>$this->slug
		]);
		return true;
	}

	
	public static function getCategory($where = null, $order = null, $limit = null, $filds = '*'){
		return Crud::Ready('tb_blog_categoria',$where,$order,$limit,$filds);
	}

	
}

?>