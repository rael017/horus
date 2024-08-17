<?php
namespace App\Controllers\Admin;

use App\Utils\MainView;

class Error extends Page{
    public static function getError($request){
		$queryParams = $request->getQueryParams();
        $msg = '';
        $error = $queryParams['code'];
        if($error == '403'){
            $msg = 'Acesso Negado: Você não possui as permissões necessárias para acessar esta página ou recurso. Caso acredite que isso é um erro, entre em contato com o administrador do sistema para obter assistência."';
        }

        $content = MainView::render('Admin/Error/error',[
            'type'=>$error,
            'msg'=>$msg,
        ]);

        return parent::getPage('Erro '.$error,$content);
    }
}


?>