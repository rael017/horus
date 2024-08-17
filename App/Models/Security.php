<?php 
namespace App\Models;

class Security
{
	public static function clear($input)
	{
	
	 strip_tags($input);
	  
	}
}

?>