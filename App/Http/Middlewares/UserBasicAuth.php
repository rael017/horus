<?php

namespace App\Http\Middlewares;

use \App\Models\Entity\User;
use Exception;

class UserBasicAuth
{
/**
 * metodo responsavel por executar o middleware
 * @param Request $request
 * @param Closure
 * @return Response 
 */

 private function getBasicUser() {
   if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
       return false;
   }

   // Verifica se o usuário existe antes de prosseguir
   $obUser = User::getUserByEmail($_SERVER['PHP_AUTH_USER']);
   if (!$obUser instanceof User || !$obUser->email) {
       return false;
   }

   // Verifica se a senha corresponde à senha hash armazenada
   if (!password_verify($_SERVER['PHP_AUTH_PW'], $obUser->senha)) {
       return false;
   }

   // Autenticação bem-sucedida, retorna o usuário autenticado
   return $obUser;
}

 private function basicAuth($request){
   if($obUser = $this->getBasicUser()){
      $request->user = $obUser;
      return $obUser;
   }
   throw new Exception('usuario ou senhas incorretos',403);
 }

 public function handle($request, $next){
   $this->basicAuth($request);
    return $next($request);
 }
}

?>