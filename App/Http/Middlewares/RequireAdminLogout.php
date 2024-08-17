<?php

namespace App\Http\Middlewares;

use \App\Sessions\Admin\Login as UsersLoginAdmin;
class RequireAdminLogout
{
	public function handle($request,$next)
	{
		if(UsersLoginAdmin::isLogged()){
			$request->getRoute()->redirect('/Admin');
			
		}
		
		return $next($request);
	}
}

?>