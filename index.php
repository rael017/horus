<?php

require __DIR__ . '/vendor/autoload.php';
if (!defined('HORUS_ROOT')) {
    define('HORUS_ROOT', __DIR__);
}

// Carrega a função de bootstrap
require_once HORUS_ROOT . '/Includes/Bootstrap.php';

// 1. Cria a aplicação usando a mesma função que o Nexus
$app = create_horus_app();

// 2. Executa a aplicação e envia a resposta
$response = $app->run();
$response->send();