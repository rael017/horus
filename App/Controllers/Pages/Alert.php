<?php 

namespace App\Controllers\Pages;

use App\Utils\MainView;


class Alert
{
/**
 * retorna a rota do site
 * @return srting

 */


 
 public static function getSucces($mensagem)
 {
	 
	
	return MainView::render('Pages/Alert/status',[
		'Type'=>'succes',
		'mensagem'=>$mensagem
		 
	 ]);
	 
 }
 
  
 public static function getError($mensagem)
 {
	 
	
	return MainView::render('Pages/Alert/status',[
		'Type'=>'error',
		'mensagem'=>$mensagem
		 
	 ]);
	 
 }
 
 

}

?>