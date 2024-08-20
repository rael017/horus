<?php
namespace App\Sessions\Admin;

class Config{


    protected static function init()
    {
        session_set_cookie_params([
            'path' => '/',
            'domain' => '', // Defina seu domínio, se necessário
            'secure' => true, // Só enviar cookies através de HTTPS
            'httponly' => true, // Cookie acessível apenas via HTTP
            'samesite' => 'Strict' // Restringir cookie ao mesmo site
        ]);

        if (session_status() != PHP_SESSION_ACTIVE) {
            session_set_cookie_params(0); // Define o cookie para expirar ao fechar o navegador
            session_start();
        }

        // Verificar se a sessão precisa ser regenerada
        if (!empty($_SESSION['last_regeneration']) && time() > $_SESSION['last_regeneration'] + 60*60*2) {
            self::my_session_regenerate_id();
        }

        // Do not allow to use too old session ID
        if (!empty($_SESSION['deleted_time']) && $_SESSION['deleted_time'] < time() - 60*60*4) {
            self::logoutUser(); // Logout se a sessão estiver muito antiga
        }
    }

    protected static function my_session_regenerate_id()
    {
        // Salvar os dados da sessão atual
        $backup = $_SESSION;

        // Regenerar o ID da sessão
        session_regenerate_id(true);

        // Restaurar os dados da sessão
        $_SESSION = $backup;

        // Atualizar o tempo de exclusão da sessão
        $_SESSION['deleted_time'] = time();

        // Atualizar o tempo da última regeneração
        $_SESSION['last_regeneration'] = time();
    }

     public static function logoutUser()
    {
        self::init();

        // Destruir todos os dados da sessão relacionados ao usuário admin
        unset($_SESSION['admin']['usuario']);
        unset($_SESSION['admin']['chave_unica']);

        // Remover o cookie seguro
        setcookie('admin_session_key', '', time() - 1, '/', '', true, true);

        // Destruir a sessão completamente
        session_destroy();

        return true;
    }
}