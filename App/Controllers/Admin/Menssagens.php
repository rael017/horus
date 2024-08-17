<?php


namespace App\Controllers\Admin;


use App\Utils\MainView;

use App\Models\DataBase\Crud;
use App\Models\Entity\Menssagens as EntityMsg;
use App\Models\DataBase\Pagination;

class Menssagens  extends Page
{
	public static function getMsg($request)
	{
		$content = MainView::render('Admin/Modulos/menssagens/index',[
			'item'=>self::getMsgItens($request,$obPagination),
			'pagination'=>parent::getPagination($request,$obPagination)
			
		]);
		
		return parent::getPainel('Painel - Lenons',$content,'Menssagens');
	}

	private static function getMsgItens($request,&$obPagination){
		$itens = '';
		$qtdTotal = EntityMsg::getMsg(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
		$queryParans = $request->getQueryParams();
		$paginaAtual = $queryParans['page'] ?? 1;
	
	
		$obPagination = new Pagination($qtdTotal,$paginaAtual,10);
		$results = EntityMsg::getMsg(null,'id DESC',$obPagination->getLimit());
		$addedEmails = []; // Array para armazenar e-mails dos clientes já adicionados

		while ($obMsg = $results->fetchObject(EntityMsg::class)) {
			// Verifica se o e-mail do cliente já foi adicionado, se sim, pula para a próxima iteração
			if (in_array($obMsg->email, $addedEmails)) {
				continue;
			}
	
			$addedEmails[] = $obMsg->email;

			$itens .= MainView::render('Admin/Modulos/menssagens/pages/item',[
				'id'       		 =>$obMsg->id,
				'nome'     		 =>$obMsg->email,
				'menssagem'      =>$obMsg->menssagem,
				'data'      =>date('d/m/Y H:i:s',strtotime($obMsg->data))
				 ]);
		}
		return $itens;
	 }

	 public static function getSingleMsg($request, $id) {
		$results = EntityMsg::getMsg($id);
	
		if (!$results || $results->rowCount() === 0) {
			$request->getRoute()->redirect('Admin/menssagens');
		}
	
		$content = '';
	
		while ($obMsg = $results->fetchObject(EntityMsg::class)) {
			$content .= MainView::render('Admin/Modulos/menssagens/response', [
				'nome'      => $obMsg->nome,
				'menssagem' => $obMsg->menssagem,
				'data'      => date('d/m/Y H:i:s', strtotime($obMsg->data))
			]);
		}
	
		return parent::getPainel('Painel - Lenons', $content, 'noticias');
	}
}