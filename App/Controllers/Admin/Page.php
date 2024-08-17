<?php 

namespace App\Controllers\Admin;

use App\Http\Request;
use App\Utils\MainView;


class Page
{
/**
 * retorna a rota do site
 * @return srting

 */


 
	public static function getPage($titulo,$content)
		{
		
		
		return MainView::render('Admin/page',[
			
			"titulo"=>$titulo,
			"content"=>$content
		]);
		
		}
	public static function getPainel($titulo,$content)
		{
			$contentPanel = MainView::render('Admin/painel',[
				
				'content'=>$content
			]);
			return self::getPage($titulo,$contentPanel);
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
			
			$links .= MainView::render('Admin/Pagination/link',[
				'page'=>$page['page'],
				'link'=>$link
			]);
			}
				return MainView::render('Admin/Pagination/box',[
				'links'=>$links
			]);
		}

	public static function getSearch($request,$obPagination)
 		{
			$pages = $obPagination->getPages();

			if(count($pages) <= 1) return '';

			$links = '';
			$url = $request->getRoute()->getCurrentUrl();

			$queryParams = $request->getQueryParams();

			foreach($pages as $page){

				
				$queryParams['search'] = $page['search'];

				$link = $url.'?'.http_build_query($queryParams);
				
				$links .= MainView::render('Admin/Search/link',[
					'search'=>$page['search'],
					'link'=>$link
				]);
				}
					return MainView::render('Admin/Search/box',[
					'links'=>$links
				]);
 		}
	


}

?>