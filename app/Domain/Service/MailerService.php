<?php

namespace App\Domain\Service;

use App\Config\Config;
use PHPMailer\PHPMailer\PHPMailer;

class MailerService
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $smtp = Config::smtp();
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host = $smtp['host'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $smtp['user'];
        $this->mailer->Password = $smtp['pass'];
        $this->mailer->SMTPSecure = $smtp['secure']; // 'ssl' or 'tls'
        $this->mailer->Port = $smtp['port'];
        $this->mailer->setFrom($smtp['fromEmail'], $smtp['fromName']);
        $this->mailer->isHTML(true);
        $this->mailer->CharSet = 'UTF-8';
    }

    public function send(string $toEmail, string $subject, string $html): bool
    {
        try {
            $this->mailer->clearAllRecipients();
            $this->mailer->addAddress($toEmail);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $html;
            return $this->mailer->send();
        } catch (\Throwable $e) {
            return false;
        }
    }
}
