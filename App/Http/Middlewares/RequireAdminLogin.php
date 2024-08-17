<?php

namespace App\Http\Middlewares;

use \App\Sessions\Admin\Login as UsersLoginAdmin;
class RequireAdminLogin
{
	public function handle($request,$next)
	{
		if(!UsersLoginAdmin::isLogged()){
			$request->getRoute()->redirect('/Admin/login');
			
		}
		
		return $next($request);
	}
}

?>