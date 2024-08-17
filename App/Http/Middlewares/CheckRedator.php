<?php  
namespace App\Http\Middlewares;

class CheckRedator extends CheckAccess
{
    public function __construct()
    {
        parent::__construct('Redator');
    }
}


?>