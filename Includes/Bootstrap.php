<?php
// Ficheiro: bootstrap/app.php

use Core\Http\Router;
use Core\Http\Request as HorusRequest;

/**
 * Esta função é a única fonte da verdade para a criação da sua aplicação.
 * Ela prepara e retorna uma instância do Router pronta para ser executada.
 * @return Router
 */
function create_horus_app(): Router
{
    // 1. Carrega as configurações do seu ficheiro principal.
    // As variáveis ($request, $middlewareMap, etc.) serão definidas no escopo local desta função.
    require_once HORUS_ROOT . '/Includes/App.php';

    // 2. Cria a instância do Router, usando as variáveis que acabaram de ser definidas
    // pelo require_once. Não é necessário usar 'global'.
    $obRouter = new Router(
        $request,
        URL, // A sua constante URL
        $middlewareMap,
        $defaultMiddlewares
    );

    // 4. Define as rotas, passando o router como argumento
     (function() use ($obRouter) {
        require HORUS_ROOT . '/Routes/Pages.php';
    })();


    // 5. Retorna a instância da aplicação pronta
    return $obRouter;
}