<?php

namespace App\Controllers\Admin;


use \App\Utils\MainView;
use App\Models\Entity\Post as EntityPost;
use App\Models\Entity\category as EntityCategory;
use App\Models\DataBase\Crud;
use App\Models\DataBase\Pagination;

class Noticias extends Page
{
	public static function getPost($request)
	{
		
		$content = MainView::render('Admin/Modulos/noticias/index',[
			'item'=>self::getPostItens($request,$obPagination),
			
			'pagination'=>parent::getPagination($request,$obPagination)
		]);
		
		return parent::getPainel('Painel - Lenons',$content,'Analize');
	}

	public static function getNewPost($request,$msg =  null)
	{
		$status = !is_null($msg) ? Alert::getError($msg) : '';
			
			$content = MainView::render('Admin/Modulos/noticias/cadastrar-noticia',[
				'itens-noticia' =>self::getFormItens($request,$obPagination),
				'title'         =>'Cadastrar Noticia',
				'status'        =>$status,
				'itens-cadastro'=>self::getCategoryPost($request,$obPagination)
			]);
			return parent::getPainel('Painel - Lenons',$content,'noticias');
	
	
	}

	private static function getPostItens($request,&$obPagination){
		$itens = '';
		$qtdTotal = EntityPost::getPost(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
		$queryParans = $request->getQueryParams();
		$paginaAtual = $queryParans['page'] ?? 1;
	
	
		$obPagination = new Pagination($qtdTotal,$paginaAtual,10);
		$results = EntityPost::getPost(null,'id DESC',$obPagination->getLimit());
	
		while($obPost = $results->fetchObject(EntityPost::class)){
			$itens .= MainView::render('Admin/Modulos/noticias/pages/item',[
				'id'       =>$obPost->id,
				'categoria'=>$obPost->categoria,
				'autor'    =>$obPost->autor,
				'titulo'   =>$obPost->titulo,
				'data'     =>date('d/m/Y H:i:s',strtotime($obPost->data)),
				 ]);
		}
		return $itens;
	 }

	 private static function getFormItens($request,&$obPagination){
		$itens = '';
		$qtdTotal = EntityPost::getPost(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
		$queryParans = $request->getQueryParams();
		$paginaAtual = $queryParans['page'] ?? 1;
	
	
		$obPagination = new Pagination($qtdTotal,$paginaAtual,5);
		$results = EntityPost::getPost(null,'id DESC',$obPagination->getLimit());
	
		while($obPost = $results->fetchObject(EntityPost::class)){
			$itens .= MainView::render('Admin/Modulos/noticias/pages/itens-noticia',[
				'id'     =>$obPost->id,
				'autor'  =>$obPost->autor,
				'titulo' =>$obPost->titulo,
				'data'   =>date('d/m/Y H:i:s',strtotime($obPost->data)),
				 ]);
		}
		return $itens;
	 }

	
	 
	 public static function insertPost($request)
	{
    $postVars = $request->getPostVars();
    $file = $request->getFilesVars();

    if (isset($postVars['enviar'])) {
        $titulo = $postVars['titulo'];
        $post = $postVars['post'];
        $categoria = $postVars['categoria'];
		
        $titleExists = Crud::Ready('tb_lenon_blog', 'titulo = "' . $titulo . '"');
        $postExists = Crud::Ready('tb_lenon_blog', 'post  = "' . $post . '"');

        $titleExistsInAnalize = Crud::Ready('tb_blog_analize', 'titulo = "' . $titulo . '"');
        $postExistsInAnalize = Crud::Ready('tb_blog_analize', 'post  = "' . $post . '"');

        if ($titleExists->rowCount() != 0 || $titleExistsInAnalize->rowCount() != 0) {
            return self::getNewPost($request, 'O título já existe');
        } elseif ($postExists->rowCount() != 0 || $postExistsInAnalize->rowCount() != 0) {
            return self::getNewPost($request, 'O post já existe');
        } elseif (empty($titulo) || trim($titulo) == '') {
            return self::getNewPost($request, 'Campos vazios não são permitidos');
        } elseif (empty($post) || trim($post) == '') {
            return self::getNewPost($request, 'Campos vazios não são permitidos');
        } else {
            
            
				$upload = new \App\Utils\Upload($file['imagem']);
				$dir = dirname(dirname(dirname(__DIR__))).'/Resources/Views/Components/images/codeblog';

				$uploadSuccess = $upload->upload($dir);

				if($uploadSuccess) {
					$nomeImagem = $upload->getBasename();					
				} else {
					return  self::getNewPost($request,'Erro Ao Fazer Upload Da Imagem');
					exit; 
				}
                  

            $obPost = new EntityPost;
            $obPost->categoria = $categoria;
            $obPost->autor = $_SESSION['admin']['usuario']['nome'];
            $obPost->titulo = $titulo;
            $obPost->post = $post;
            $obPost->imagem = $nomeImagem;  // Salve o caminho da imagem
            $obPost->slug = \App\Utils\Slug::generateSlug($titulo);
            $obPost->registerInAnalize();
            return  self::getNewPost($request);
        }
    }
    return self::getPost($request);
}



	 private static function getCategoryItens($request,&$obPagination){
		$itens = '';
		$qtdTotal = EntityCategory::getCategory(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
		$queryParans = $request->getQueryParams();
		$paginaAtual = $queryParans['page'] ?? 1;
	
	
		$obPagination = new Pagination($qtdTotal,$paginaAtual,5);
		$results = EntityCategory::getCategory(null,'id DESC',$obPagination->getLimit());
	
		while($obCategory = $results->fetchObject(EntityCategory::class)){
			$itens .= MainView::render('Admin/Modulos/noticias/pages/itens-categoria',[
				'id'     =>$obCategory->id,
				'nome'   =>$obCategory->nome,
				'slug'   =>$obCategory->slug,
				
				 ]);
		}
		return $itens;
	 }
	 

	 public static function getNewCategory($request,$msg = null)
	 {
		
		$status = !is_null($msg) ? Alert::getError($msg) : '';
		$content = MainView::render('Admin/Modulos/noticias/cadastrar-categoria',[
			'itens-categoria'=>self::getCategoryItens($request,$obPagination),
			'title'=>'Gerenciar Categoria',
			'status'=>$status,
		]);
		
		return parent::getPainel('Painel - Lenons',$content,'noticias');
	 }


	 public static function insertCategory($request){
		$postVars = $request->getPostVars();
	
		if(isset($postVars['acao'])){
			
			$nome = strlen($postVars['name-category']) ? strip_tags($postVars['name-category']) : '';
			$categoryExists = Crud::Ready('tb_blog_categoria','nome = "'.$nome.'"');
	
			if($categoryExists->rowCount() != 0){
				return self::getNewCategory($request,'o titulo já existe');
				exit;
	
			}else{
				$obcategory = new EntityCategory;
	
				
				$obcategory->nome = $nome;
				$obcategory->slug   = \App\Utils\Slug::generateSlug($nome);
				$obcategory->cadastrar();
				return $request->getRoute()->redirect('/Admin/noticias/cadastrar-categoria?success');
				exit;
			}
			
		}
		return self::getPost($request);
		
	 } 


	 private static function getCategoryPost($request,&$obPagination){
		$itens = '';
		
		$results = EntityCategory::getCategory(null,'id DESC',$obPagination->getLimit());
	
		while($obPost = $results->fetchObject(EntityCategory::class)){
			$itens .= MainView::render('Admin/Modulos/noticias/pages/itens-cadastro',[
				'categoria'  =>$obPost->nome,
			 ]);
		}
		return $itens;
	 }
	
}

?>