<?php
/**
 * Mail.php
 *------------------------------------------------------
 *
 * 
 *
 * PHP versions 5
 *
 *
 *
 * @author Willing Peng<pcq2006@gmail.com>
 * @copyright (C) 2013-2018 
 * @version CVS: Id: Mail.php,v 1.0 2013-10-6 下午10:15:17 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace Custom\Util;

use Zend\Mail as ZendMail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class Mail 
{
    protected $mail;
    protected $transport;
    
    public function __construct() 
    {
        $this->mail = new ZendMail\Message();
        $this->mail->setFrom('MM_SI_SMCN_Noreply@valueclickbrands.com');
        $this->transport = new ZendMail\Transport\Sendmail();
    }
    
    /**
     * 发送html邮件
     *
     * @param string $recipient
     * @param array $from
     * @param string $subject
     * @param string $template
     * @param array $data
     * @param string $cc
     * @return $this
     */
    public function sendHtml($recipient, $from=array(), $subject, $message, $cc=false)
    {
        $text = new MimePart($message);
        $text->type = "text/html";
        
        $body = new MimeMessage();
        $body->setParts(array($text));
        
        $this->mailMessage($recipient, $from, $subject, $body, $cc);
        $this->transport->send($this->mail);
        return $this;
    }
    
    /**
     * load the template and send the message
     *
     * @param string $recipient
     * @param array $from
     * @param string $subject
     * @param string $template
     * @param array $data
     * @param string $cc
     * @return $this
     */
    public function mailMessage($recipient, $from=array(), $subject, $message, $cc=false) 
    {
        $this->mail->setSubject($subject);
        $this->mail->setBody($message);
        $this->mail->addTo($recipient);
        if ($cc) {
            $this->mail->addCc($cc);
        }
        $this->mail->setFrom($from);
        return $this;
    }
}
?>