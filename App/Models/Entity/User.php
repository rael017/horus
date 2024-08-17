<?php

namespace App\Models\Entity;

use \App\Models\DataBase\Crud;
class User
{
	public $id;
	public $nome;
	public $email;
	public $senha;
	public $cargo;
	public $ip;
	

	public function setUser()
	{
		
		$this->id = Crud::Create('tb_lenon_user',[
			'nome'		   	    =>$this->nome,
			'email'				=>$this->email,
			'senha'        		=>$this->senha,
			'cargo' 			=>$this->cargo,
			'ip'				=>'192.199.199'
			
		]);
		return true;
	}

	 public function Atualizar()
    {
        return Crud::Update('tb_lenon_user', 'id = '.$this->id, [
            'nome'    	=> $this->nome,
            'email'     => $this->email,
            'senha' 	=> $this->senha,
            'cargo'     => $this->cargo
        ]);
    }

	public function excluir()
	{
		return	Crud::Delete('tb_lenon_user','id = '.$this->id);
		
	}

	public static function getUsers($where = null, $order = null, $limit = null, $filds = '*'){
		return Crud::Ready('tb_lenon_user',$where,$order,$limit,$filds);
	}
	
	public static function getUserByEmail($email)
	{
		return Crud::Ready('tb_lenon_user','email = "'.$email.'"')->fetchObject(self::class);
	}

	public static function getUserById($id)
	{
		return Crud::Ready('tb_lenon_user','id = "'.$id.'"')->fetchObject(self::class);
	}

	public static function nameExists($nome)
    {
        return Crud::Ready('tb_lenon_user', 'nome = "' . $nome . '"')->rowCount() > 0;
    }

    // Verifica se o email existe na tabela tb_lenon_user
    public static function emailExists($email)
    {
        return Crud::Ready('tb_lenon_user', 'email = "' . $email . '"')->rowCount() > 0;
    } 

	public function getCargo()
    {
        return $this->cargo;
    }

    public static function getCargoHierarchy()
    {
        return [
            'Adiministrador-Geral' => 1,
            'Sub-Adiministrador' => 2,
            'Redator' => 3
        ];
    }
	
}

?>