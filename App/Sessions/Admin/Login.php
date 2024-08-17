<?php
namespace App\Sessions\Admin;

class Login extends Config
{
   

    public static function login($User)
    {
        Parent::init();

        // Verificar se já há uma sessão ativa para o usuário
        if (self::isLogged()) {
            self::logout(); // Encerrar sessão anterior se houver login simultâneo
            die("Sessão anterior encerrada. Efetue o login novamente.");
        }

        // Iniciar nova sessão para o usuário logado
        $_SESSION['admin']['usuario'] = [
            'id' => $User->id,
            'nome' => $User->nome,
            'email' => $User->email,
            'senha' => $User->senha,
            'cargo' => $User->cargo,
            'ip'    => $User->ip
        ];

        // Gerar uma chave única para esta sessão
        $_SESSION['admin']['chave_unica'] = uniqid();

        // Armazenar a chave única em um cookie seguro
        setcookie('admin_session_key', $_SESSION['admin']['chave_unica'], 0, '/', '', true, true);

        return true;
    }

    public static function isLogged()
    {
        Parent::init();

        // Verificar se a chave única da sessão no cookie corresponde à sessão atual
        if (isset($_COOKIE['admin_session_key']) && isset($_SESSION['admin']['chave_unica'])) {
            return ($_COOKIE['admin_session_key'] === $_SESSION['admin']['chave_unica']);
        }

        return false;
    }
    

     public static function storeConfirmationCode($code)
    {
        Parent::init();
        $_SESSION['confirmation_code'] = $code;
        $_SESSION['confirmation_time'] = time();
    }

    public static function getConfirmationCode()
    {
        Parent::init();
        if (isset($_SESSION['confirmation_time']) && (time() - $_SESSION['confirmation_time']) > 180) {
            self::clearConfirmationData();
            return null;
        }
        return $_SESSION['confirmation_code'] ?? null;
    }

    public static function storeUserData($email, $senha)
    {
        Parent::init();
        $_SESSION['user_data'] = ['email' => $email, 'senha' => $senha];
    }

    public static function getUserData()
    {
        Parent::init();
        if (isset($_SESSION['confirmation_time']) && (time() - $_SESSION['confirmation_time']) > 180) {
            self::clearConfirmationData();
            return null;
        }
        return $_SESSION['user_data'] ?? null;
    }

    public static function clearConfirmationData()
    {
        unset($_SESSION['confirmation_code']);
        unset($_SESSION['confirmation_time']);
        unset($_SESSION['user_data']);
    }

    

    public static function logout()
    {
        Parent::logoutUser();
    }
}
?>

