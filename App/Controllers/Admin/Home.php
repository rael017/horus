<?php

namespace App\Controllers\Admin;

use \App\Utils\MainView;
use \App\Models\DataBase\Crud;
use \App\Models\Entity\Post as EntityPost;

class Home extends Page
{
	public static function getHome($request)
	{

		
		
		$visitasTotais = Crud::Ready('tb_blog_visitas',null,null,null,'SUM(visitas) as views')->fetchObject()->views;
		$visitashoje = Crud::Ready('tb_blog_visitas','dia = "'.date('y/m/d').'"' ,null,null,'SUM(visitas) as viewsDay')->fetchObject()->viewsDay;
		$content = MainView::render('Admin/Modulos/home/index',[
			'vday'=>$visitashoje,
			'vtotal'=>$visitasTotais,
			'item'=>self::setTop($request)
		]);
		
		return parent::getPainel('Painel - Lenons',$content,'HOME');
	}

	public static function setTop($request){
		$itens = '';
		$results = EntityPost::topTre();
	    
		while($obPost = $results->fetchObject()){
			$itens .= MainView::render('Admin/Modulos/home/pages/item',[
				'data'       =>date('d/m/Y H:i:s',strtotime($obPost->data)),
				'categoria'  =>$obPost->categoria,
				'autor'      =>$obPost->autor,
				'titulo'	 =>$obPost->titulo
				 ]);
		}
		return $itens;
	}

	
	
}

?>