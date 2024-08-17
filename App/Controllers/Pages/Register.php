<?php
namespace App\Controllers\Pages;

use \App\Utils\MainView;

class Register extends Page
{
	public static function getRegister()
	{
		
		
		$content = MainView::render('Pages/cadastrar',[
			"nome"=>'lenons',
			"descricao"=>'Blog sobre Tecnologia'
			 ]);

		return parent::getPage('LenonsGrife - Cadastro',$content);
	}
}

?>