<?php

namespace App\Models\Entity;

use \App\Models\DataBase\Crud;
class Clientes
{
	public $id;
	public $nome;
	public $email;
	public $celular;

	
    

	public function setClient(){
		$this->id = Crud::Create('tb_lenon_clientes',[
			'nome'              =>$this->nome,
			'email' 		    =>$this->email,
			'celular'			=>$this->celular
		]);
		return true;
	}

	   public function checkIfExists()
    {
        // Verificar se o e-mail já existe no banco de dados
        $existingClient = Crud::Ready('tb_lenon_clientes', ['email' => $this->email, 'celular' => $this->celular]);

        // Se já existe um cliente com o mesmo e-mail, retorna verdadeiro
        return !empty($existingClient);
    }
   

}