<?php
namespace BackEnd\Model\System;

use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use BackEnd\Model\System\ConfigTable;
class Mail
{
	protected $mail;
	protected $transport;
	private $options;
	
	public function __construct()
	{
		$this->mail = new Message();
		$this->mail->setFrom('MM_SI_SMCN_Noreply@valueclickbrands.com');
		$this->transport = new SmtpTransport();
		
		$connection_config = array(
				'username' => ConfigTable::getSysConf('smtp_user'),
				'password' => ConfigTable::getSysConf('smtp_pass'),
		);
		if (ConfigTable::getSysConf('smtp_ssl')) {
			$connection_config['ssl'] = 'ssl';
		}
		$this->options = new SmtpOptions(array(
				'name' => 'localhost',
				'host' => ConfigTable::getSysConf('smtp'),
				'port' => ConfigTable::getSysConf('port'),
				'connection_class' => 'login',
				'connection_config' => $connection_config
		));
		$this->transport->setOptions($this->options);
	}
	public function setOptions($options)
	{
		$this->options = $options;	
		$this->transport->setOptions($this->options);
	}
	public function sendHtml($to, $subject, $htmlMarkup)
    {
    	$html = new MimePart($htmlMarkup);
    	$html->type = "text/html";
    	$body = new MimeMessage();
    	$body->setParts(array($html));
    	
    	if (is_array($to)) {
    		foreach ($to as $t) {
    			$this->mail->addTo($t);
    		}
    	} else {
    		$this->mail->addTo($to);
    	}
    	$this->mail->addFrom(ConfigTable::getSysConf('smtp_user'))
    	->setSubject($subject)
    	->setBody($body);
    	$this->transport->send($this->mail);
    }
    public function addEmail($to)
    {
    	$this->mail->addTo($to);
    }
}