<?php 

namespace App\Controllers\Pages;

use App\Utils\MainView;


class Page

{
/**
 * retorna a rota do site
 * @return srting

 */

 public static function getHeader()
 {
	 return MainView::render('Pages/templates/header');
 }

 public static function getFooter()
 {
	 return MainView::render('Pages/templates/footer');
 }

 public static function getPage($titulo,$content)
 {
	 


	return MainView::render('Pages/index',[
		
		 "titulo"=>$titulo,
		 "header"=>self::getHeader(),
		 "content"=>$content,
		 "footer"=>self::getFooter()
		 
	 ]);
	 
 }

 public static function getPagination($request,$obPagination)
 {
	$pages = $obPagination->getPages();

	if(count($pages) <= 1) return '';

	$links = '';
	$url = $request->getRoute()->getCurrentUrl();

	$queryParams = $request->getQueryParams();

	foreach($pages as $page){

		
		$queryParams['page'] = $page['page'];

		$link = $url.'?'.http_build_query($queryParams);
		
		$links .= MainView::render('Pages/Pagination/link',[
			'page'=>$page['page'],
			'link'=>$link
		]);
	}
	return MainView::render('Pages/Pagination/box',[
		'links'=>$links
	]);
 }
 

}

?>