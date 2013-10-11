<?php
/**
 * AjaxController.php
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
 * @version CVS: Id: AjaxController.php,v 1.0 2013-9-20 上午11:07:06 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace FrontEnd\Controller;

use Custom\Mvc\Controller\AbstractActionController;
use Custom\Util\Utilities;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use FrontEnd\Model\Users\MemberTable;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mvc\View\Console\ViewManager;
use FrontEnd\Model\System\ConfigTable;

class AjaxController extends AbstractActionController
{
	public function checkAction()
	{
		$rs = array();
		$s = $this->params()->fromQuery('s');
		switch ($s) {
			case 'user':
				$userName = $this->params()->fromPost('name');
				$table = $this->_getTable('MemberTable');
				$count = $table->checkExist(array('UserName' => $userName));
				$rs['code'] = $count ? 1 : 0;
				$rs['msg'] = $count;
				break;
			case 'forg':
				$userId = $this->params()->fromPost('id');
				$table = $this->_getTable('MemberTable');
				$memberInfo = $table->getOneById($userId);
				$actKey = md5($memberInfo->Password).base64_encode(time());
				$url = Utilities::get_domain()."/forget-confirm.do?uid={$memberInfo->UserID}&actKey=".$actKey;
				$htmlMarkup = "打开地址：$url";
				$html = new MimePart($htmlMarkup);
				$html->type = "text/html";
				$body = new MimeMessage();
				$body->setParts(array($html));
				
				$message = new Message();
				$message->addTo($memberInfo->Email)
				->addFrom(ConfigTable::getSysConf('smtp_user'))
				->setSubject('Email地址验证')
				->setBody($body);
				
				$transport = new SmtpTransport();
				
				$connection_config = array(
						'username' => ConfigTable::getSysConf('smtp_user'),
						'password' => ConfigTable::getSysConf('smtp_pass'),
				);
				if (ConfigTable::getSysConf('smtp_ssl')) {
					$connection_config['ssl'] = 'ssl';
				}
				$options = new SmtpOptions(array(
						'name' => 'localhost',
						'host' => ConfigTable::getSysConf('smtp'),
						'port' => ConfigTable::getSysConf('port'),
						'connection_class' => 'login',
						'connection_config' => $connection_config
				));
				$transport->setOptions($options);
				$transport->send($message);
				$rs['code'] = 0;
				$rs['msg'] = '邮件已发送至'.$memberInfo->Email.',请登录邮箱进行验证';
				$rs['url'] = '/register-n';
			default:
				break;
		}
		
		print_r(json_encode($rs));die;
	}
}