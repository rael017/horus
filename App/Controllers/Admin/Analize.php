<?php


namespace App\Controllers\Admin;


use App\Utils\MainView;
use App\Models\Entity\Post as EntityPost;
use App\Models\Entity\category as EntityCategory;
use App\Models\DataBase\Pagination;
use App\Utils\Slug;

class Analize  extends Page
{
	public static function getPost($request,$type = null, $msg =  null)
	{
		if($type == 'success'){
			$status = !is_null($msg) ? Alert::getSucces($msg) : '';
		}else{
			$status = !is_null($msg) ? Alert::getError($msg) : '';
		}
		

		
		$noticia = self::getAnalizeItens($request,$obPagination,$status);
		

		$content = MainView::render('Admin/Modulos/analize/index',[
			'noticia'=>$noticia,
			'pagination'=>parent::getPagination($request,$obPagination),
			'status'=>$status,
			
		]);
		
		
		return parent::getPainel('Analizar noticias',$content,'noticias');
	}

	public static function getPostAnalize($request,$type = null, $msg =  null)
	{
		if($type == 'success'){
			$status = !is_null($msg) ? Alert::getSucces($msg) : '';
		}else{
			$status = !is_null($msg) ? Alert::getError($msg) : '';
		}

		$postVars = $request->getQueryParams();
		if(strlen(@$postVars['search'])){
			$noticia = self::search($request,$postVars['search'],$obPagination);
		}else{
			$noticia = self::getItens($request,$obPagination,$status);
		}

		$content = MainView::render('Admin/Modulos/analize/analizar-noticias',[
			'noticias'=>$noticia,
			'pagination'=>parent::getPagination($request,$obPagination),
			'status'=>$status,
			'busca'=>@$postVars['search']
			
		]);
		
		return parent::getPainel('Painel - Lenons',$content,'noticias');
	}

	
	
	 public static function setPost($request){

		$postVars = $request->getPostVars();
		
		$id = $postVars['id'];
		$obPost = EntityPost::getForAnalize($id);
		if(!$obPost instanceof EntityPost){
			return self::getPost($request,'error','objeto não existe');

		}else if(isset($postVars['Excluir'])){
			$obPost->excluirAnalize();
			return $request->getRoute()->redirect('/Admin/analize/analizar-noticias');
		}else if(isset($postVars['Publicar'])){
			$obPost->excluirAnalize();
			$obPost->autor     = $obPost->autor;
			$obPost->titulo    = $postVars['titulo'] ?? $obPost->titulo;
			$obPost->post      = $postVars['post'] ?? $obPost->post;
			$obPost->categoria = $postVars['categoria'] ?? $obPost->categoria;
			$obPost->slug      = Slug::generateSlug($obPost->titulo);
			$obPost->data      = date('d/m/Y H:i:s');
			$obPost->publicar();
			
			return $request->getRoute()->redirect('/Admin/analize/analizar-noticias');
			
		}

		
	 }


	 public static function getEditPost($request,$id,$type = null, $msg =  null){
 
		if($type == 'success'){
			$status = !is_null($msg) ? Alert::getSucces($msg) : '';
		}else{
			$status = !is_null($msg) ? Alert::getError($msg) : '';
		}
		
		$obPost = EntityPost::getForEdit($id);

		if(!$obPost instanceof EntityPost){
			$request->getRoute()->redirect('/Admin/analize');
		}
		
		
		$content = MainView::render('Admin/Modulos/analize/editar',[
			
			'title'    =>'Editar Post',
			'titulo'   =>$obPost->titulo,
			'categoria'=>$obPost->categoria,
			'post'     =>stripslashes($obPost->post),
			'itens-categoria' =>self::getCategoryPost($request,$obPagination),
			'status'	=>$status
			
		]);
		
		
		
		return parent::getPainel('Painel - Lenons',$content,'noticias');
	 
	}

	

	 public static function setEditPost($request,$id){

		
		$obPost = EntityPost::getForEdit($id);

		if(!$obPost instanceof EntityPost){
			$request->getRoute()->redirect('/Admin/analize');
		}
		
		$postVars = $request->getPostVars();

		$obPost->titulo = $postVars['titulo'] ?? $obPost->titulo;
		$obPost->post = $postVars['post'] ?? $obPost->post;
		$obPost->categoria = $postVars['categoria'] ?? $obPost->categoria;
		$obPost->AtualizarPost();
		$request->getRoute()->redirect('/Admin/analize/analizar-noticias');
		
	 }

	  public static function getDeletePost($request,$id){

		
		$obPost = EntityPost::getForEdit($id);

		if(!$obPost instanceof EntityPost){
			$request->getRoute()->redirect('/Admin/analize');
		}
		
		
		$content = MainView::render('Admin/Modulos/analize/excluir',[
			'title'    =>'Excluir Post',
			'titulo'   =>$obPost->titulo,
			'categoria'=>$obPost->categoria,
			'post'     =>stripslashes($obPost->post),
			'itens-categoria' =>self::getCategoryPost($request,$obPagination)
		]);
		
		
		
		return parent::getPainel('Painel - Lenons',$content,'noticias');
	 }

	  public static function setDeletePost($request,$id){

		
		$obPost = EntityPost::getForEdit($id);

		if(!$obPost instanceof EntityPost){
			$request->getRoute()->redirect('/Admin/analize/analizar-noticias');
		}
		
		$obPost->excluir();
		$request->getRoute()->redirect('/Admin/analize/analizar-noticias');
		
	 }

	

	 private static function getCategoryPost($request,&$obPagination){
		$itens = '';
		$qtdTotal = EntityCategory::getCategory(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
		$queryParans = $request->getQueryParams();
		$paginaAtual = $queryParans['page'] ?? 1;
	
	
		$obPagination = new Pagination($qtdTotal,$paginaAtual);
		$results = EntityCategory::getCategory(null,'id DESC',$obPagination->getLimit());
	
		while($obPost = $results->fetchObject(EntityCategory::class)){
			$itens .= MainView::render('Admin/Modulos/analize/pages/itens-categoria',[
				'name'  =>$obPost->nome,
				
				 ]);
		}
		return $itens;
	 }


	 
	 private static function getAnalizeItens($request,&$obPagination,$status){
		$itens = '';
		$qtdTotal = EntityPost::getAnalize(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
		$queryParans = $request->getQueryParams();
		$paginaAtual = $queryParans['page'] ?? 1;
	
	
		$obPagination = new Pagination($qtdTotal,$paginaAtual,1);
		$results = EntityPost::getAnalize(null,'id ASC',$obPagination->getLimit());
	
		while($obPost = $results->fetchObject(EntityPost::class)){
			$itens .= MainView::render('Admin/Modulos/analize/pages/analize-noticias',[
				'id'       =>$obPost->id,
				'categoria'=>$obPost->categoria,
				'categorias'=>self::getCategoryPost($request,$obPagination),
				'autor'    =>$obPost->autor,
				'titulo'   =>$obPost->titulo,
				'post'     =>$obPost->post,
				'status'   =>$status
				 ]);
		}
		return $itens;
	 }

	 private static function getItens($request,&$obPagination,$status){
		$itens = '';
		$qtdTotal = EntityPost::getPost(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
		$queryParans = $request->getQueryParams();
		$paginaAtual = $queryParans['page'] ?? 1;
		
		$obPagination = new Pagination($qtdTotal,$paginaAtual,5);
	    $results = EntityPost::getPost(null,'id ASC',$obPagination->getLimit());
		
		
	
		while($obPost = $results->fetchObject(EntityPost::class)){
			$itens .= MainView::render('Admin/Modulos/analize/pages/item',[
				'id'       =>$obPost->id,
				'autor'    =>$obPost->autor,
				'titulo'   =>$obPost->titulo,
				'data'     =>$obPost->data,
				'status'   =>$status
				 ]);
		}
		return $itens;
	 }


	 private static function search($request,$arg,&$obPagination)
	 {

		$itens = '';
		
		$busca = strip_tags($arg) ;

		

		$condicoes = [
			strlen($busca) ? 'titulo LIKE "%'.str_replace(' ','%',$busca).'%"' : null
		];

		$condicoes = array_filter($condicoes);

		$where = implode(' AND ',$condicoes);

		$qtdTotal = EntityPost::getPost($where,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
		$queryParans = $request->getQueryParams();
		$paginaAtual = $queryParans['page'] ?? 1;
		
		$obPagination = new Pagination($qtdTotal,$paginaAtual,5);
	    $results = EntityPost::getPost($where,null,$obPagination->getLimit());
		
		while($obPost = $results->fetchObject(EntityPost::class)){
			$itens .= MainView::render('Admin/Modulos/analize/pages/item',[
				'id'       =>$obPost->id,
				'autor'    =>$obPost->autor,
				'titulo'   =>$obPost->titulo,
				'data'     =>$obPost->data,
				
				 ]);
		}
		return $itens;
	 }
	

}