<?php
/**
 * RegisterController.php
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
 * @version CVS: Id: RegisterController.php,v 1.0 2013-9-28 下午11:14:18 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace FrontEnd\Controller;

use Custom\Mvc\ActionEvent;
use Zend\Db\ResultSet\ResultSet;
use Custom\Db\TableGateway\TableGateway;
use Custom\Mvc\Controller\AbstractActionController;
use Custom\Util\Utilities;
use FrontEnd\Model\Users\MemberTable;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mvc\View\Console\ViewManager;
use FrontEnd\Model\System\ConfigTable;


class RegisterController extends AbstractActionController
{
    function indexAction()
    {
    	$this->layout('layout/register');
    	
    	$req = $this->getRequest();
    	$container = $this->_getSession('member');
    	if ($container->UserID) {
    		$v = new ViewModel(array('UserID' => $container->UserID));
    		$v->setTemplate('front-end/register/step2');
    		return $v;
    	}
    	
    	if ($req->isPost()) {
    		$params = $req->getPost();
    		$chk = 1;
    		$errMsg = '';
    		if (!isset($params['UserName']) || empty($params['UserName'])) {
    			$chk = 0;
    			$errMsg = '请输入用户名';
    		} elseif (!isset($params['password']) || empty($params['password'])) {
    			$chk = 0;
    			$errMsg = '请输入密码';
    		} elseif (!isset($params['repassword']) || empty($params['repassword'])) {
    			$chk = 0;
    			$errMsg = '请输入确认密码';
    		}
    		$memberTable = $this->_getTable('MemberTable');
    		if ($memberTable->checkExist(array('UserName' => trim($params['UserName'])))) {
    			$chk = 0;
    			$errMsg = '用户名已存在';
    		}
    		
    		if ($chk) {
    			$captcha_sess = $this->_getSession('Zend_Form_Captcha_'.$_SESSION['captcha_code']);
    			if (isset($_SESSION['captcha_code']) && $captcha_sess->word == $params['reg_code']) {
    				$now = date('Y-m-d H:i:s');
    				$memberTable->insert(array('UserName' => trim($params['UserName']), 
    						'Password' => md5($params['password']), 
    						'AddTime' => $now, 'LastLogin' => $now,
    						'Source' => 1,
    						'LoginCount' => 1,
    						));
    				$UserID = $memberTable->getLastInsertValue();
    				$memberInfo = $memberTable->getOneById($UserID);
    				$container = $this->_getSession('member');
    				$container->UserID = $UserID;
    				$container->UserName = $memberInfo->UserName;
    				$v = new ViewModel(array('UserID' => $UserID));
    				$v->setTemplate('front-end/register/step2');
    				return $v;
    			} else {
    				$errMsg = '验证码不正确';
    				$this->_message($errMsg, 'error');
    				return $this->redirect()->toUrl('/register');
    			}
    		} else {
    			$this->_message($errMsg, 'error');
    			return $this->redirect()->toUrl('/register');
    		}
    		
    	}
//     	$v = new ViewModel(array('UserID' => 1));
//     	$v->setTemplate('front-end/register/step2');
//     	return $v;
        return array();
    }
	function captchaAction()
	{
		unset($_SESSION['captcha_code']);
		$captcha = new \Zend\Captcha\Image(array(
				'Expiration' => '300',
				'wordlen' => '4',
				'Height' => '28',
				'Width' => '77',
				'writeInFile'=>false,
				'Font' => APPLICATION_PATH.'/data/AdobeSongStd-Light.otf',
				'FontSize' => '24',
				'DotNoiseLevel' => 10,
				'ImgDir' => '/images/FrontEnd'
		));
		//设置验证码保存路径
// 		$captcha->setImgDir('D:/www/project/code/public/images/FrontEnd');
		//生成验证码
		$imgName = $captcha->generate();
		$_SESSION['captcha_code'] = $imgName;
		die;
// 		echo '/images/FrontEnd/'.$imgName.'.png';die;
		//获取验证码内容且输出
// 		echo $captcha->getWord();
	}
	function emailAction()
	{
		$req = $this->getRequest();
		$params = $req->getPost();
		$rs = array('code' => 0, 'msg' => '');
		if (isset($params['email']) && preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$/', trim($params['email']))) {
			$container = $this->_getSession('member');
			$email = trim($params['email']);
			$memberTable = $this->_getTable('MemberTable');
			if ($memberTable->checkExist(array('Email' => $email), $container->UserID)) {
				$rs = array('code' => 1, 'msg' => "邮箱地址{$email}已被占用，请更换其他可用邮箱！");
			} else {
				$memberTable->update(array('Email' => $email, 'isValidEmail' => 0, 'LastUpdate' => Utilities::getDateTime()), array('UserID' => $container->UserID));
				
				$code = $container->UserID.'||'.$email.'||'.time();
				$code = base64_encode($code);
				
				$url = Utilities::get_domain()."/register-success?uid={$container->UserID}&code=".base64_encode($code);
					
				$htmlMarkup = ConfigTable::getSysConf('emailActTemplate');
// 				$htmlMarkup = '<p style="color:#ff0000;">感谢注册!</p>';
// 				$htmlMarkup .= "<p>点击链接进行激活操作：<a href='{$url}'>激活确认链接</a></p>";
				$htmlMarkup = str_replace(array('{userName}', '{email}', '{url}'), array($container->UserName, $email, $url), $htmlMarkup);
				
				$html = new MimePart($htmlMarkup);
				$html->type = "text/html";
				$body = new MimeMessage();
				$body->setParts(array($html));
				
				$message = new Message();
				$message->addTo($email)
				->addFrom(ConfigTable::getSysConf('smtp_user'))
				->setSubject('Email地址验证')
				->setBody($body);
				
				$transport = new SmtpTransport();
				
// 				$sys_config = array();
// 				if (file_exists('./data/sys_config.php')) {
// 					$sys_config = include'./data/sys_config.php';
// 				}
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
				$rs['msg'] = '邮件已发送至'.$email.',请登录邮箱进行验证';
				$rs['url'] = '/register-n';
			}
			
		} else {
			$rs = array('code' => -1, 'msg' => '邮箱格式不正确！');
		}
		
		$v = new JsonModel($rs);
		$v->setTerminal(true);
		return $v;
	}
	public function nextAction()
	{
		$this->layout('layout/register');
		
		$memberTable = $this->_getTable('MemberTable');
		
		$container = $this->_getSession('member');
		$memberInfo = $memberTable->getOneById($container->UserID);
		$v = new ViewModel($memberInfo->toArray());
		$v->setTerminal(false);
		return $v;
	}
	public function successAction()
	{
		$this->layout('layout/register');
		$uid = $this->params()->fromQuery('uid' , '');
		$code = $this->params()->fromQuery('code' , '');
		$memberTable = $this->_getTable('MemberTable');
		$memberInfo = $memberTable->getOneById($uid);
		
		$deStr = base64_decode(base64_decode($code));
		$r = explode("||", $deStr);
		if (isset($r[2]) && time()-$r[2] > 48*60*60) {
			header('Content-Type: text/html; charset=utf-8');
			die('抱歉，验证链接已过期！');
		}
		if (isset($r[0]) && isset($r[1]) && $r[0] == $memberInfo->UserID && $r[1] == $memberInfo->Email) {
			$memberTable->update(array('isValidEmail' => 1), array('UserID' => $uid));
			return new ViewModel($memberInfo->toArray());
		} else {
			header('Content-Type: text/html; charset=utf-8');
			die('验证失败，或者地址已失效');
		}
	}
}