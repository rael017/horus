<?php 

namespace App\Controllers\Api;

class Api{
    public static function getDetails($request)
    {
        return [
            'nome'=>'Api - codeMind',
            'versao'=>'v1.0.0',
            'autor'=>'Raphael Ferreira'
        ];
    }
    
    protected static function getPagination($request,$obPagination)
    {
        $queryParams = $request->getQueryParams();

        $pages = $obPagination->getPages();

        return[
            'paginaAtual'=> isset($queryParams['page']) ? $queryParams['page'] : 1,
            'qtdPaginas' => !empty($pages) ? count($pages) : 1
        ];
    }
}

?>