<?php
namespace App\Http\Middlewares;

use \App\Models\Entity\User;
use Exception;

class CheckAccess
{
    protected $requiredRole;

    public function __construct(string $requiredRole)
    {
        $this->requiredRole = $requiredRole;
    }

    public function handle($request, $next)
    {
        // Certifique-se de que o usuário autenticado está sendo corretamente atribuído ao request
        $user = $request->userAdmin;

        if (!$user instanceof User) {
            throw new Exception('Usuário não autenticado', 403);
        }

        // Obtém a hierarquia de cargos
        $cargoHierarchy = User::getCargoHierarchy();

        // Adiciona um log para depuração
        error_log("Cargo Hierarchy: " . print_r($cargoHierarchy, true));

        // Obtém o cargo do usuário
        $userCargo = $user->cargo;

        if (!isset($cargoHierarchy[$userCargo])) {
            throw new Exception('Cargo do usuário não encontrado'.$userCargo , 403);
        }

        $userCargoLevel = $cargoHierarchy[$userCargo];
        $requiredRoleLevel = $cargoHierarchy[$this->requiredRole] ?? null;

        // Adiciona um log para depuração
        error_log("User Cargo: $userCargo, User Cargo Level: $userCargoLevel");
        error_log("Required Role: {$this->requiredRole}, Required Role Level: $requiredRoleLevel");

        if ($requiredRoleLevel === null) {
            throw new Exception('Cargo requerido não encontrado', 403);
        }

        // Verifica se o cargo do usuário tem permissão
        if ($userCargoLevel <= $requiredRoleLevel) {
            return $next($request);
        }

        $request->getRoute()->redirect('/Admin/error?code=403');
    }
}
?>
