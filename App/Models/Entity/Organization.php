<?php

namespace App\Models\Entity;

use \App\Models\DataBase\Crud;

class Organization{
    public $id;
	public $pagina;
	public $modelo;
	public $titulo;
	public $descricao;
	public $imagem;
	public $status;

	

	public function setPage()
	{
		
		$this->id = Crud::Create('tb_lenon_site',[
			'pagina'		=>$this->pagina,
			'modelo'		=>$this->modelo,
			'titulo'        =>$this->titulo,
			'descricao' 	=>$this->descricao,
			'imagem'   		=>$this->imagem,
			'status'		=>$this->status
			
			
			
		]);
		return true;
	}

	public function Atualizar()
	{
		return	Crud::Update('tb_lenon_site','id = '.$this->id,[
			'pagina' 		=>$this->pagina,
			'modelo'		=>$this->modelo,
			'titulo'   		=>$this->titulo,
			'descricao'   	=>$this->descricao,
			'imagem'        =>$this->imagem,
			'status'		=>$this->status
		]);
		
	}

	public static function getPage($where = null, $order = null, $limit = null, $filds = '*'){
		return Crud::Ready('tb_lenon_site',$where,$order,$limit,$filds);
	}

	public static  function getSinglePage($pagina){
		return self::getPage('pagina = "'.$pagina.'" AND status = "ativo"')->fetchObject(self::class);
	}

	public static  function getForEdit($id){
		return self::getPage('id = '.$id)->fetchObject(self::class);
	}
	
}

?>