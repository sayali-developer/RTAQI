<?php
/**
 * TO USE THIS CLASS SECRETS.PHP FILE MUST HAVE BEEN SET UP PROPERLY
 */

namespace RTAQI\Framework\Classes;


use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    private PHPMailer $phpmailer;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->phpmailer = new PHPMailer();
        $this->phpmailer->isSMTP();
        $this->phpmailer->SMTPAuth = true;
        $this->phpmailer->isHTML(true);
        $this->phpmailer->Host = SMTP_HOST;
        $this->phpmailer->Username = SMTP_USER;
        $this->phpmailer->Password = SMTP_PASSWORD;
        $this->phpmailer->Port = SMTP_PORT;
        $this->phpmailer->setFrom(SMTP_DEFAULT_EMAIL, SENDER_DEFAULT_NAME);
        if (SMTP_SECURE_PROTOCOL == 'tls') {
            $this->phpmailer->SMTPAutoTLS = true;
        } else {
            $this->phpmailer->SMTPAutoTLS = false;
        }
        $this->phpmailer->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

    }

    /**
     * @return PHPMailer
     */
    public function getPhpmailer(): PHPMailer
    {
        return $this->phpmailer;
    }

}