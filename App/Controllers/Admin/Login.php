<?php
namespace App\Controllers\Admin;

use \App\Utils\MainView;
use \App\Models\Entity\User;
use \App\Sessions\Admin\Login as UsersLoginAdmin;
use \App\Utils\Email;

class Login extends Page
{
    // Renderiza a página de login
    public static function getLogin($request, $mensagem = null)
    {
        $status = !is_null($mensagem) ? Alert::getError($mensagem) : '';
        
        $content = MainView::render('Admin/Login/login', [
            'status' => $status
        ]);

        return parent::getPage('CodeMind - Login', $content);
    }

    // Lida com o login do usuário
   // Lida com o login do usuário
	public static function setLogin($request)
	{
		$postVars = $request->getPostVars();
		
		$email = $postVars['email'] ?? '';
		$senha = $postVars['senha'] ?? '';
		$confirmar = $postVars['confirmar'] ?? '';

		// Verifica se a senha e a confirmação coincidem antes de consultar o banco de dados
		if ($senha !== $confirmar) {
			return self::getLogin($request, 'As senhas não coincidem.');
		}
		
		$User = User::getUserByEmail($email);

		if (!$User instanceof User) {
			return self::getLogin($request, 'Usuário ou senha inválidos.');
		}
		
		if (!password_verify($senha, $User->senha)) {
			return self::getLogin($request, 'Usuário ou senha inválidos.');
		}
		
		// Verificar se já está logado antes de permitir novo login
		if (UsersLoginAdmin::isLogged()) {
			UsersLoginAdmin::logout(); // Encerrar sessão anterior
		}
		
		// Realizar o login
		UsersLoginAdmin::login($User);

        
		
		$request->getRoute()->redirect('/Admin');

        
	}

	public static function setLoginSecure($request)
    {
        $postVars = $request->getPostVars();
        
        $email = $postVars['email'] ?? '';
        $senha = $postVars['senha'] ?? '';
        $confirmar = $postVars['confirmar'] ?? '';

        // Verifica se a senha e a confirmação coincidem antes de consultar o banco de dados
        if ($senha !== $confirmar) {
            return self::getLogin($request, 'As senhas não coincidem.');
        }
        
        $User = User::getUserByEmail($email);

        if (!$User instanceof User) {
            return self::getLogin($request, 'Usuário ou senha inválidos.');
        }
        
        if (!password_verify($senha, $User->senha)) {
            return self::getLogin($request, 'Usuário ou senha inválidos.');
        }

        $currentIp = $request->getIpAddressStored();

        // Verificar se o IP é diferente do IP armazenado no banco de dados
        if ($User->ip && $User->ip !== $currentIp) {
            // Enviar código de confirmação por e-mail
            $code = self::generateConfirmationCode();
            $emailSent = self::sendConfirmationEmail($User, $code);

            if (!$emailSent) {
                return self::getLogin($request, 'Erro ao enviar o código de confirmação por e-mail.');
            }

            // Armazenar o código de confirmação em sessão para comparação posterior
            UsersLoginAdmin::storeConfirmationCode($code);
            UsersLoginAdmin::storeUserData($email, $senha);
            
            // Redirecionar para a página de confirmação
            return self::getConfirm($request, $email);
        }
        
        // Verificar se já está logado antes de permitir novo login
        if (UsersLoginAdmin::isLogged()) {
            UsersLoginAdmin::logout(); // Encerrar sessão anterior
        }
        
        // Realizar o login
        UsersLoginAdmin::login($User);
        UsersLoginAdmin::clearConfirmationData();
        $request->getRoute()->redirect('/Admin');
    }


    // Lida com o logout do usuário
    public static function setLogout($request)
    {
        UsersLoginAdmin::logout();
        
        $request->getRoute()->redirect('/Admin/login');
    }

	public static function getConfirm($request, $mensagem = null)
    {
		$status = !is_null($mensagem) ? Alert::getError($mensagem) : '';

        $content = MainView::render('Admin/Login/confirm', [
            'status' => $status
        ]);

        return parent::getPage('Confirmação de Login', $content);
    }

	public static function setConfirm($request)
    {
        $postVars = $request->getPostVars();
        $code = $postVars['code'] ?? '';

        if ($code == UsersLoginAdmin::getConfirmationCode()) {
            $userData = UsersLoginAdmin::getUserData();
            $User = User::getUserByEmail($userData['email']);

            if ($User instanceof User && password_verify($userData['senha'], $User->senha)) {
                UsersLoginAdmin::login($User);
				UsersLoginAdmin::clearConfirmationData();
                $request->getRoute()->redirect('/Admin');
            }
        }

        return self::getConfirm($request, 'Código de confirmação inválido.');
    }

	private static function sendConfirmationEmail($user, $confirmationCode)
    {
        $subject = 'Confirmação de Login';
        $message = "Seu código de confirmação é: $confirmationCode";

        $email = new Email();
        $email->addRecipient($user->email, $user->nome);  // Adiciona o destinatário
        $email->setSubject($subject);  // Define o assunto do e-mail
        $email->setBody($message);  // Define o corpo do e-mail
        $email->setAltBody(strip_tags($message));  // Define o corpo alternativo do e-mail
        
        return $email->send(); // Retorna true se o e-mail for enviado com sucesso
    }

	private static function generateConfirmationCode()
    {
        return substr(md5(uniqid(mt_rand(), true)), 0, 8); // Exemplo simples de geração de código
    }

}