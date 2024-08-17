<?php

namespace App\Http\Middlewares;

class CheckAdministradorGeral extends CheckAccess
{
    public function __construct()
    {
        parent::__construct('Adiministrador-Geral');
    }
}

?>