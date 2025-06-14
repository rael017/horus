<?php
namespace App\Sessions\Admin;

use Predis\Client;

class Config {
    private static $redis;
    private static $sessionTTL = 1800; // Tempo padrão de expiração da sessão (30 minutos)

    /**
     * Inicializa Redis e configura parâmetros da sessão
     */
    private static function initRedis() {
        if (!self::$redis) {
            self::$redis = new Client();
            self::$redis->connect('127.0.0.1', 6379);
        }
    }

    /**
     * Inicia uma sessão segura
     */
    public static function init() {
        self::initRedis();

        session_set_cookie_params([
            'lifetime' => 0,  // Expira ao fechar o navegador
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Verifica se a sessão está registrada no Redis
        if (!isset($_SESSION['session_key']) || !self::$redis->exists($_SESSION['session_key'])) {
            self::logoutUser(); // Se não existir, desloga o usuário
        }

        // Atualiza o tempo de expiração no Redis
        self::$redis->expire($_SESSION['session_key'], self::$sessionTTL);
    }

    /**
     * Cria uma nova sessão segura e armazena no Redis
     * 
     * @param string $sessionName Nome da sessão (ex: 'admin', 'user')
     * @param array $data Dados a serem armazenados na sessão
     * @param int|null $ttl Tempo de expiração da sessão (padrão: 30 min)
     */
    public static function createSession(string $sessionName, array $data, int $ttl = null) {
        self::initRedis();
        
        if ($ttl) {
            self::$sessionTTL = $ttl;
        }

        session_start();
        session_regenerate_id(true);

        $sessionKey = "session_{$sessionName}_" . session_id();

        $_SESSION['session_key'] = $sessionKey;
        $_SESSION['session_name'] = $sessionName;

        self::$redis->setex($sessionKey, self::$sessionTTL, json_encode($data));
    }

    /**
     * Obtém os dados da sessão armazenados no Redis
     */
    public static function getSessionData() {
        self::initRedis();

        if (!isset($_SESSION['session_key']) || !self::$redis->exists($_SESSION['session_key'])) {
            return null;
        }

        return json_decode(self::$redis->get($_SESSION['session_key']), true);
    }

    /**
     * Destroi a sessão e remove do Redis
     */
    public static function logoutUser() {
        self::initRedis();

        if (isset($_SESSION['session_key'])) {
            self::$redis->del($_SESSION['session_key']);
        }

        session_unset();
        session_destroy();
    }
}
