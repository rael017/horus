<?php 

namespace App\Sessions\Admin;
use \App\Models\Entity\User;

class Usuarios extends Config
{
    // Armazenar dados temporários para cadastro de usuário
    public static function storeTempUserData($userVars)
    {
        Parent::init();

        $token = bin2hex(random_bytes(16));
        $tokenExpireTime = time() + 180; // 3 minutos em segundos

        $_SESSION['user_registration_token'] = $token;
        $_SESSION['user_registration_token_expire_time'] = $tokenExpireTime;
        $_SESSION['user_registration_data'] = $userVars;

        return $token;
    }

    // Verificar o token e retornar os dados do usuário
    public static function verifyToken($token)
    {
        Parent::init();

        if (!isset($_SESSION['user_registration_token']) || !isset($_SESSION['user_registration_token_expire_time'])) {
            return ['status' => 'error', 'message' => 'Token inválido ou expirado'];
        }

        if ($_SESSION['user_registration_token'] !== $token) {
            return ['status' => 'error', 'message' => 'Token inválido'];
        }

        if (time() > $_SESSION['user_registration_token_expire_time']) {
            // Token expirado, limpar sessão
            unset($_SESSION['user_registration_token']);
            unset($_SESSION['user_registration_token_expire_time']);
            unset($_SESSION['user_registration_data']);
            return ['status' => 'error', 'message' => 'Token expirado'];
        }
 
        $userVars = $_SESSION['user_registration_data'];

        // Limpar sessão
        unset($_SESSION['user_registration_token']);
        unset($_SESSION['user_registration_token_expire_time']);
        unset($_SESSION['user_registration_data']);

        return ['status' => 'success', 'userVars' => $userVars];
    }

     public static function getUser()
     {

        parent::init();
        if (!\App\Sessions\Admin\Login::isLogged()) {
            throw new \Exception('Usuário não autenticado', 403);
        }

        // Obtém o ID do usuário da sessão
        $userId = $_SESSION['admin']['usuario']['id'];

        // Busca o usuário pelo ID
        $user = User::getUserById($userId);

        if (!$user instanceof User) {
            throw new \Exception('Usuário não encontrado', 404);
        }

        return $user;
    }

    public static function getUserCargo(){
        return $_SESSION['admin']['usuario']['cargo'];
    }
}

