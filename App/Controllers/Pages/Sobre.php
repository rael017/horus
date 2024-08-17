<?php 

namespace App\Controllers\Pages;

use App\Utils\MainView;


class Sobre extends Page

{
	


	/**
 * retorna a pagina sobre do site
 * @return srting

 */

 public static function getSobre(){
	$content = MainView::render('Pages/sobre',[
	"nome"=>'lenons',
	"descriçao"=>'Blog sobre Tecnologia'
		 
	 ]);
	 return  parent::getPage('Blog - Lenons - Sobre',$content,'header');
	
 }
 
 	
	
	
		
	
}

?>