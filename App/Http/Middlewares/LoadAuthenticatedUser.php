<?php
namespace App\Http\Middlewares;

use \App\Sessions\Admin\Usuarios as UsersLoginAdmin;
use \App\Models\Entity\User;
use Exception;

class LoadAuthenticatedUser
{
    public function handle($request, $next)
    {
        

        // Obtém o usuário autenticado da sessão
        $user = UsersLoginAdmin::getUser();

        // Adiciona o usuário autenticado ao request
        $request->userAdmin = $user;

        return $next($request);
    }
}
?>