<?php

namespace App\Models\Entity;
use \PDO;
use \App\Models\DataBase\Crud;
class Post
{
	public $id;
	public $categoria;
	public $autor;
	public $titulo;
	public $post;
	public $imagem;
	public $data;
	public $slug;
	
	
	
	public function registerInAnalize()
    {
        $this->data = date('y-m-d H:i:s');
        $this->id = Crud::Create('tb_blog_analize', [
            'categoria' => $this->categoria,
            'autor'     => $this->autor,
            'titulo'    => $this->titulo,
            'post'      => $this->post,
            'imagem'    => $this->imagem,
        ]);
        return true;
    }

    public function publicar()
    {
        $this->data = date('y-m-d H:i:s');
        $this->id = Crud::Create('tb_lenon_blog', [
            'categoria' => $this->categoria,
            'autor'     => $this->autor,
            'titulo'    => $this->titulo,
            'post'      => $this->post,
            'imagem'    => $this->imagem,
            'data'      => $this->data,
            'slug'      => $this->slug
        ]);
        return true;
    }

    public function Atualizar()
    {
        return Crud::Update('tb_blog_analize', 'id = '.$this->id, [
            'titulo'    => $this->titulo,
            'post'      => $this->post,
            'categoria' => $this->categoria,
            'imagem'    => $this->imagem
        ]);
    }

    public function AtualizarPost()
    {
        return Crud::Update('tb_lenon_blog', 'id = '.$this->id, [
            'titulo'    => $this->titulo,
            'post'      => $this->post,
            'categoria' => $this->categoria,
            'imagem'    => $this->imagem
        ]);
    }

	public function excluirAnalize()
	{
		return	Crud::Delete('tb_blog_analize','id = '.$this->id);
		
	}

	public function excluir()
	{
		return	Crud::Delete('tb_lenon_blog','id = '.$this->id);
		
	}

	public static function getPost($where = null, $order = null, $limit = null, $filds = '*'){
		return Crud::Ready('tb_lenon_blog',$where,$order,$limit,$filds);
	}
	
	public static  function getForEdit($id){
		return self::getPost('id = '.$id)->fetchObject(self::class);
	}

    public static function getPostsByCategory($category, $limit = null) {
        $where = 'categoria = "' . $category . '"';
        $order = 'id DESC';

        return self::getPost($where, $order, $limit, '*')->fetchObject(self::class);
    }


	public static  function getForPost($slug){
		return self::getPost('slug = "'.$slug.'"')->fetchObject(self::class);
	}

	public static  function getForTitulo($titulo){
		return self::getPost('titulo = "'.$titulo.'"');
	}

	public static function getAnalize($where = null, $order = null, $limit = null, $filds = '*'){
		return Crud::Ready('tb_blog_analize',$where,$order,$limit,$filds);
	}


	public static  function getForAnalize($id){
		return self::getAnalize('id = '.$id)->fetchObject(self::class);
	}

	public static function topTre(){
		return Crud::SelectInner('tb_lenon_blog','tb_blog_visitas','*','slug','pagina','visitas DESC','3');
	}


	
}

?>