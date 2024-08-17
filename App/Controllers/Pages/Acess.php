<?php

namespace App\Controllers\Pages;

use \App\Utils\MainView;

class Acess extends Page
{
	public static function getAcess()
	{
		
		
		$content = MainView::render('Pages/entrar',[
			"nome"=>'lenons',
			"descricao"=>'Blog sobre Tecnologia'
			 ]);
			 
		
			 
			 
		return parent::getPage('LenonsGrife - Login',$content);
	}
}

?>