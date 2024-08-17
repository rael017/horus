<?php

namespace App\Models\Entity;

use \App\Models\DataBase\Crud;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
class Menssagens
{
	public $id;
	public $nome;
	public $email;
	public $menssagem;
	public $data;
	
	
    public static function getMsg($where = null, $order = null, $limit = null, $filds = '*') {
        return Crud::Ready('tb_lenon_menssagens',$where, $order, $limit, $filds);
    }

    public static  function getSingleMsg($id){
		return self::getMsg('id = '.$id)->fetchObject(self::class);
	}

     public static function getClientMsgs($clientEmail) {
        return Crud::Ready('tb_lenon_menssagens', ['email' => $clientEmail], 'data ASC', null, '*');
    }

	public function sendMsg(){
		$this->id = Crud::Create('tb_lenon_menssagens',[
			'nome'              =>$this->nome,
			'email'				=>$this->email,
			'menssagem' 		=>$this->menssagem,
			'data'				=>$this->data = date('Y-m-d H:i:s')
		]);

		
		return true;
	}


	public function sendEmail(){
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'seu_servidor_smtp';  // Configure com as informações do seu servidor SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'seu_email@gmail.com';  // Seu e-mail
            $mail->Password = 'sua_senha';  // Sua senha
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use PHPMailer::ENCRYPTION_SMTPS se necessário
            $mail->Port = 587;  // A porta do seu servidor SMTP

            $mail->setFrom('seu_email@gmail.com', 'Seu Nome');
            $mail->addAddress($this->email, $this->nome);  // Adicione o e-mail do destinatário aqui

            $mail->isHTML(true);
            $mail->Subject = 'Assunto do E-mail';
            $mail->Body = 'Corpo do E-mail';

            $mail->send();
        } catch (Exception $e) {
            // Trate o erro ou registre em logs
            return false;
        }
    }
	
   

}