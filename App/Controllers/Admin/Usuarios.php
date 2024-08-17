<?php
 
namespace App\Controllers\Admin;

use \App\Utils\MainView;
use \App\Utils\Email;
use \App\Models\DataBase\Pagination;
use \App\Models\DataBase\Crud;
use \App\Models\Entity\User as EntityUser;


class Usuarios extends Page{

    public static function getUsers($request)
	{
		
		
		$content = MainView::render('Admin/Modulos/usuarios/index',[
	
			'item'=>self::getItensEdit($request,$obPagination),
			'pagination'=>parent::getPagination($request,$obPagination)
		]);
		
		return parent::getPainel('Painel - Lenons',$content,'site');
		
	}

	 private static function getItensEdit($request,&$obPagination){
		$itens = '';
		$qtdTotal = EntityUser::getUsers(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
		$queryParans = $request->getQueryParams();
		$paginaAtual = $queryParans['page'] ?? 1;
	
	
		$obPagination = new Pagination($qtdTotal,$paginaAtual,5);
		$results = EntityUser::getUsers(null,'id DESC',$obPagination->getLimit());
	
		while($obUser = $results->fetchObject(EntityUser::class)){
	

			$itens .= MainView::render('Admin/Modulos/usuarios/pages/item-edit',[
				'id'		=>$obUser->id,
				'nome'      =>$obUser->nome,
				'email'     =>$obUser->email,
				'cargo'     =>$obUser->cargo
			]);
		}
		return $itens;
	 }

	public static function getNewUser($request,$type = null,$msg = null)
	{

		if($type == 'success'){
				$status = !is_null($msg) ? Alert::getSucces($msg) : '';
			}else{
				$status = !is_null($msg) ? Alert::getError($msg) : '';
			} 


			$content = MainView::render('Admin/Modulos/usuarios/adicionar', [
				'title'    => 'cadastrar usuario',
				'status'   => $status
			]);

			$response = parent::getPainel('Painel Usuarios', $content);

		// Adiciona o script de redirecionamento após 2 segundos
		if ($type == 'success') {
			$response .= '<script>
				setTimeout(function() {
					window.location.href = "/codeMind/Admin/usuarios";
				}, 100000);
			</script>';
		}	
		return $response;
	}

	public static function setNewUser($request)
	{
		$UserVars = $request->getPostVars();

		// Verifica se o formulário foi enviado com ação definida
		if (isset($UserVars['acao'])) {
			$nome = $UserVars['nome'];
			$email = $UserVars['email'];
			$cargo = $UserVars['cargo'];
			$senha = $UserVars['senha'];

			// Verifica se algum dos campos obrigatórios está vazio
			if (empty($nome) || empty($email) || empty($cargo) || empty($senha)) {
				return self::getNewUser($request, 'error', 'Campos vazios não são permitidos');
			}

			// Verifica se o nome de usuário já existe
			if (EntityUser::nameExists($nome)) {
				return self::getNewUser($request, 'error', 'Nome de usuário já existe');
			}

			// Verifica se o email já está cadastrado
			if (EntityUser::emailExists($email)) {
				return self::getNewUser($request, 'error', 'Email já cadastrado');
			}

			// Cria um novo objeto de usuário e atribui os valores
			$obUser = new EntityUser;
			$obUser->nome = $nome;
			$obUser->email = $email;
			$obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
			$obUser->cargo = $cargo;
			
			// Chama o método para salvar o usuário no banco de dados
			$obUser->setUser();

			// Redireciona para a página de sucesso após o cadastro
			return self::getNewUser($request, 'success', 'Usuário cadastrado com sucesso');
		}
	}

	public static function setUser($request)
	{
		$UserVars = $request->getPostVars();

		if (isset($UserVars['acao'])) {
			$nome = $UserVars['nome'];
			$email = $UserVars['email'];
			$cargo = $UserVars['cargo'];
			$senha = $UserVars['senha'];

			if (empty($nome) || empty($email) || empty($cargo) || empty($senha)) {
				return self::getNewUser($request, 'error', 'Campos vazios não são permitidos');
			}

			if (EntityUser::nameExists($nome)) {
				return self::getNewUser($request, 'error', 'Nome de usuário já existe');
			}

			if (EntityUser::emailExists($email)) {
				return self::getNewUser($request, 'error', 'Email já cadastrado');
			}

			// Armazenar dados temporários e gerar o token
			$token = \App\Sessions\Admin\Usuarios::storeTempUserData($UserVars);

			$link = URL . '/Admin/verificar?token=' . $token;
			$subject = 'Verificação de Cadastro';
			$message = 'Clique no link para verificar seu cadastro: ' . $link;

			$emailUtil = new Email();
			$emailUtil->addRecipient($email);
			$emailUtil->setSubject($subject);
			$emailUtil->setBody($message);

			$sendResult = $emailUtil->send();

            if ($sendResult === true) {
                return self::getNewUser($request, 'success', 'Verificação de e-mail enviada com sucesso');
            } else {
                return self::getNewUser($request, 'error', $sendResult); // Exibir mensagem de erro
            }
   		}
	}	


	public static function getVerification($request,$type = null,$msg = null)
	{
		if($type == 'success'){
				$status = !is_null($msg) ? Alert::getSucces($msg) : '';
			}else{
				$status = !is_null($msg) ? Alert::getError($msg) : '';
			}


			$content = MainView::render('Admin/Login/verificar', [
				'status'   => $status
			]);

			$response = parent::getPage('novo usuario', $content);

		// Adiciona o script de redirecionamento após 2 segundos
		if ($type == 'success') {
			$response .= '<script>
				setTimeout(function() {
					window.location.href = "/codeMind/Admin/login";
				}, 100000);
			</script>';
		}	
		return $response;

	}

	public static function setVerification($request)
	{
		$token = $request->getQueryParams()['token'];

		$result = \App\Sessions\Admin\Usuarios::verifyToken($token);

		if ($result['status'] === 'error') {
			return self::getVerification($request, 'error', $result['message']);
		}

		// Token válido, continuar com o cadastro
		$UserVars = $result['userVars'];
		$nome = $UserVars['nome'];
		$email = $UserVars['email'];
		$cargo = $UserVars['cargo'];
		$senha = $UserVars['senha'];

		$obUser = new EntityUser;
		$obUser->nome = $nome;
		$obUser->email = $email;
		$obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
		$obUser->cargo = $cargo;
		$obUser->ip    = $request->getIpAdressStored();
		// Chama o método para salvar o usuário no banco de dados
		$obUser->setUser();

		return self::getVerification($request, 'success', 'Olá, seja bem-vindo ' . $nome);
	}



    public static function getEditUser($request, $id = null,$type = null, $msg = null){
       if($type == 'success'){
			$status = !is_null($msg) ? Alert::getSucces($msg) : '';
		}else{
			$status = !is_null($msg) ? Alert::getError($msg) : '';
		} 

        $obUser = EntityUser::getUserById($id);

        if (!$obUser instanceof EntityUser) {
            $request->getRoute()->redirect('/Admin/usuarios');
        }

        $content = MainView::render('Admin/Modulos/usuarios/editar', [
            'title'    => 'Editar Usuario',
            'status'   => $status,
            'nome'     => $obUser->nome,
            'email'    => $obUser->email,
            'cargo'    => $obUser->cargo
        ]);

    	$response = parent::getPainel('Painel Usuarios', $content);

    // Adiciona o script de redirecionamento após 2 segundos
    if ($type == 'success') {
        $response .= '<script>
            setTimeout(function() {
                window.location.href = "/codeMind/Admin/usuarios";
            }, 2000);
        </script>';
    }


    return $response;
    }

    public static function setEditUser($request, $id)
	{
		$obUser = EntityUser::getUserById($id);
		$UserVars = $request->getPostVars();    

		if (!$obUser instanceof EntityUser) {
			return self::getEditUser($request, $id,'error', 'Usuário não existe');
		}

		// Verifica se a senha atual está correta, se fornecida
		if (isset($UserVars['laterPass']) && !password_verify($UserVars['laterPass'], $obUser->senha)) {
			return self::getEditUser($request, $id,'error', 'Senha incorreta');
		}

		// Verifica se o novo nome já existe, excluindo o nome atual do usuário
		if (EntityUser::nameExists($UserVars['nome']) && $UserVars['nome'] !== $obUser->nome) {
			return self::getEditUser($request, $id,'error', 'Nome de usuário já existe');
		}

		// Verifica se o novo email já existe, excluindo o email atual do usuário
		if (EntityUser::emailExists($UserVars['email']) && $UserVars['email'] !== $obUser->email) {
			return self::getEditUser($request, $id,'error', 'Email já cadastrado');
		}

		// Atualiza os dados do usuário
		 $obUser->nome  = isset($useVars['nome']) && !empty($useVars['nome']) ? $useVars['nome'] : $obUser->nome;
   		 $obUser->email = isset($useVars['email']) && !empty($useVars['email']) ? $useVars['email'] : $obUser->email;
	     if (isset($UserVars['newPass']) && !empty($UserVars['newPass'])) {
			$obUser->senha = password_hash($UserVars['newPass'], PASSWORD_DEFAULT);
		 }
		$obUser->cargo = $UserVars['cargo'] ?? $obUser->cargo;

		$obUser->Atualizar();

		// Regenerar o ID da sessão por segurança
		session_regenerate_id(true);

		return self::getEditUser($request,$id,'success','usuario editado com sucesso');
		
		
	}


	  public static function getDeleteUser($request, $id = null,$type = null, $msg = null){
       if($type == 'success'){
			$status = !is_null($msg) ? Alert::getSucces($msg) : '';
		}else{
			$status = !is_null($msg) ? Alert::getError($msg) : '';
		} 

        $obUser = EntityUser::getUserById($id);

        if (!$obUser instanceof EntityUser) {
            $request->getRoute()->redirect('/Admin/usuarios');
        }

        $content = MainView::render('Admin/Modulos/usuarios/excluir', [
            'title'    =>'Editar Usuario',
			'status'   => $status,
            'nome'     => $obUser->nome,
            'email'    => $obUser->email,
            'cargo'    => $obUser->cargo
        ]);

    	$response = parent::getPainel('Painel Usuarios', $content);

    // Adiciona o script de redirecionamento após 2 segundos
		if ($type == 'success') {
			$response .= '<script>
				setTimeout(function() {
					window.location.href = "/codeMind/Admin/usuarios";
				}, 2000);
			</script>';
		
		}

		return $response;


	  }

	public static function setDeleteUser($request,$id){

		
		$obUser = EntityUser::getUserById($id);

		if(!$obUser instanceof EntityUser){
			$request->getRoute()->redirect('/Admin/usuarios');
		}
		
		$obUser->excluir();
		session_regenerate_id(true);

		return self::getEditUser($request,$id,'success','usuario excluido com sucesso');
	
	}
	

}

?>