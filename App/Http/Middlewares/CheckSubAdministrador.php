<?php 
namespace App\Http\Middlewares;

class CheckSubAdministrador extends CheckAccess
{
    public function __construct()
    {
        parent::__construct('Sub-Adiministrador');
    }
}

?>