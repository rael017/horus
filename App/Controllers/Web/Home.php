<?php 
namespace App\Controllers\Web;

use Core\Utils\MainView;

class Home extends BaseController
{
	
/**
 * retorna a home do site
 * @return srting

 */


    public static function getIndex($request,$msg = null){
       return MainView::render('index');
    }

}