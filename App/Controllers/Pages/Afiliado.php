<?php 

namespace App\Controllers\Pages;

use \App\Utils\MainView;

class Afiliado extends Page
{
	public static function getAfiliado()
	{
		
		
		$content = MainView::render('Pages/afiliar-se',[
			"nome"=>'lenons',
			"descricao"=>'Seja um Afiliado Lenons'
			 ]);
			 
		
			 
			 
		return parent::getPage('LenonsGrife - Afiliar-se',$content);
	}
}


?>