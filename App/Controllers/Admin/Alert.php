<?php 

namespace App\Controllers\Admin;

use App\Utils\MainView;


class Alert
{
/**
 * retorna a rota do site
 * @return srting

 */


 
 public static function getSucces($mensagem)
 {
	 
	
	return MainView::render('Admin/Alert/status',[
		'Type'=>'succes',
		'mensagem'=>$mensagem
		 
	 ]);
	 
 }
 
  
 public static function getError($mensagem)
 {
	 
	
	return MainView::render('Admin/Alert/status',[
		'Type'=>'error',
		'mensagem'=>$mensagem
		 
	 ]);
	 
 }
 
 

}

?>