<?php
namespace App\Http\Controllers;

use PHPMailer\PHPMailer;


class Config {
    const BASE_URL = "http://127.0.0.1/";
    const MAIL_FROM = "no_reply@mail.com";
    const MAIL_FROM_NAME = "HelmetDetect_NoReply";
    const MAIL_USER_NAME = "max92051212@gmail.com"; // 用來寄信的 GMAIL 帳號
    const MAIL_USER_PASSWROD = "zfrh qjvj ajec zguu";      // 用來寄信的 GMAIL 密碼
}

class Mail extends PHPMailer\PHPMailer {
    public $Host     = 'smtp.gmail.com';
    public $Mailer   = 'smtp';
    public $SMTPAuth = true;
    public $Username = 'max92051212@gmail.com';
    public $Password = 'zfrh qjvj ajec zguu';
    public $SMTPSecure = 'tls';
    public $WordWrap = 75;

    public function __construct($Username, $Password){
        $this->Username = $Username;
        $this->Password = $Password;
    }

    public function subject($subject) {
        $this->Subject = $subject;
    }

    public function body($body) {
        $this->Body = $body;
    }

    public function send() {
        $this->AltBody = strip_tags(stripslashes($this->Body))."\n\n";
        $this->AltBody = str_replace(" ", "\n\n", $this->AltBody);
        return parent::send();
    }
}

class MailController extends Controller {
    public static function sendMail($email, $code) {
        try {
            $to = $email;
            $subject = "【HelmetDetect】忘記密碼_驗證碼發送";
            $body = "code: $code";
            $mail = new Mail(Config::MAIL_USER_NAME, Config::MAIL_USER_PASSWROD);
            $mail->setFrom(Config::MAIL_FROM, Config::MAIL_FROM_NAME);
            $mail->addAddress($to);
            $mail->subject($subject);
            $mail->body($body);

            if($mail->send()){
                return true;
            }else{
                return false;
            }
        } catch(Exception $e) {
            echo 'Caught exception: ',  $e->getMessage();
            $error[] = $e->getMessage();
      }
    }
}