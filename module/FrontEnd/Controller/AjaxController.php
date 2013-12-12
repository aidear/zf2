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
use Custom\Util\CURL;
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
				$htmlMarkup = "{$memberInfo->UserName},<p>您要求了重设您在本站的帐户密码。</p><p>您可以点击下面的链接登录，或者将此链接复制到浏览器地址栏：</p><p>$url</p><p></p>";
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
				break;
			case 'validCode':
				$sessID = $this->params()->fromPost('id');
				$code = $this->params()->fromPost('code');
// 				$captcha_sess = $this->_getSession('Zend_Form_Captcha_'.$_SESSION[$sessID]);
// 				file_put_contents('D:/a.txt', print_r($_SESSION[$sessID], true));
				if (strtolower($_SESSION[$sessID]) == strtolower($code)) {
					$rs = array('code' => 0, 'msg' => 'success');
				} else {
					$rs = array('code' => 1, 'msg' => strtolower($_SESSION[$sessID]));
				}
				break;
			case 'region':
				$pid = $this->params()->fromQuery('p');
				$regionTable = $this->_getTable('RegionTable');
				$assign = $regionTable->getRegionByPid($pid);
				$rs = $assign;
				break;
			case 'weather':
				$rs = array();
				$area = $this->params()->fromPost('area');
				$area = Utilities::unescape($area);
				$area = str_replace(array('区','县'), '', $area);
				$py = Utilities::Pinyin($area, 1);
				$url = 'http://a1.tianqi.com/index.php?c=other&a=json';
				if ($py) {
					$url .= '&py='.$py;
				}
				$curl = CURL::getInstance();
				$data = $curl->get_contents($url);
				$weather = json_decode($data, true);
				$rs['day_1'] = array(
						'temp' => $weather['temp1'],
						'weather_desc' => $weather['weather1'],
						'img' => $weather['img1'],
					);
				$rs['day_2'] = array(
						'temp' => $weather['temp2'],
						'weather_desc' => $weather['weather2'],
						'img' => $weather['img2'],
					);
				$rs['day_3'] = array(
						'temp' => $weather['temp3'],
						'weather_desc' => $weather['weather3'],
						'img' => $weather['img3'],
				);
				$rs['city'] = $weather['city_en'];
				break;
			default:
				break;
		}
		$v = new JsonModel($rs);
		return $v;
	}
}