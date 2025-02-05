<?php

namespace Core\Utils;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        // Configurações do servidor SMTP
        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.example.com';  // Substitua pelo host do seu servidor SMTP
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'seu_email@example.com';  // Substitua pelo seu endereço de e-mail
        $this->mailer->Password = 'sua_senha';  // Substitua pela sua senha
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->mailer->Port = 465;

        // Configurações de remetente e destinatário
        $this->mailer->setFrom('seu_email@example.com', 'Seu Nome');  // Substitua pelo seu endereço de e-mail e nome
        $this->mailer->addReplyTo('seu_email@example.com', 'Seu Nome');  // Substitua pelo seu endereço de e-mail e nome
    }

    public function addRecipient($email, $name = '')
    {
        $this->mailer->addAddress($email, $name);
    }

    public function setSubject($subject)
    {
        $this->mailer->Subject = $subject;
    }

    public function setBody($body)
    {
        $this->mailer->Body = $body;
    }

    public function setAltBody($altBody)
    {
        $this->mailer->AltBody = $altBody;
    }

    public function send()
    {
        try {
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            return "Erro ao enviar e-mail: {$this->mailer->ErrorInfo}";
        }
    }
}
