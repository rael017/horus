<?php
namespace Core\Console\Server;

use Swoole\Http\Server;

class ServerCommand
{
    public static function execute(array $options): void
    {
        if (!extension_loaded('swoole')) {
            echo "❌ Erro Fatal: A extensão Swoole não está carregada no seu ambiente PHP.\n";
            exit(1);
        }

        // Define que estamos a correr no modo Nexus.
        define('HORUS_NEXUS_MODE', true);
        
        $host = '0.0.0.0'; 
        $port = 9501;
        $http = new Server($host, $port);
        
        $http->set(['max_request' => 1000]);
        
        $http->on('start', function($server) {
            // Carrega o seu ponto de entrada uma vez para garantir que a função de bootstrap está disponível
            require_once HORUS_ROOT . '/index.php';
            print("🚀 Servidor Horus Nexus iniciado com sucesso em http://localhost:{$server->port}\n");
        });

        $http->on('request', function ($swooleRequest, $swooleResponse) {
            
            if (isset($swooleRequest->server['request_uri']) && $swooleRequest->server['request_uri'] == '/favicon.ico') {
                $swooleResponse->status(404)->end();
                return;
            }

            // Simula as superglobais
            $_SERVER['REQUEST_URI']    = $swooleRequest->server['request_uri'] ?? '/';
            $_SERVER['REQUEST_METHOD'] = $swooleRequest->server['request_method'] ?? 'GET';
            $_GET                      = $swooleRequest->get ?? [];
            $_POST                     = $swooleRequest->post ?? [];

            try {
                // Chama a função de bootstrap para obter uma instância fresca da aplicação
                $obRouter = bootstrap_horus_app();

                // Executa a aplicação
                $horusResponse = $obRouter->run();

                // Converte a resposta do Horus para a do Swoole
                foreach ($horusResponse->getHeaders() as $key => $value) { $swooleResponse->header($key, $value); }
                $swooleResponse->status($horusResponse->getStatusCode());
                $swooleResponse->end($horusResponse->getContent());
                
            } catch (\Throwable $e) {
                echo "❌ Erro Fatal no Pedido: " . $e->getMessage() . "\n" . $e->getTraceAsString() ."\n";
                $swooleResponse->status(500)->end("<h1>Erro Interno do Servidor</h1>");
            }
        });

        $http->start();
    }
}
