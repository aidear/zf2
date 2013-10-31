<?php
/**
 * MemberController.php
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
 * @version CVS: Id: MemberController.php,v 1.0 2013-9-18 下午11:17:43 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace FrontEnd\Controller;

use Zend\Paginator\Paginator;
use Zend\Form\Form;
use Zend\Form\Element;
use Custom\Mvc\Controller\AbstractActionController;
use Custom\Util\Utilities;
use Zend\View\Model\ViewModel;
use FrontEnd\Model\Users\Member;
use Zend\View\Model\JsonModel;
use FrontEnd\Model\System\ConfigTable;

class MemberController extends AbstractActionController
{
	public function indexAction()
	{
		$this->_isCenter();
		$userInfo = $this->_currentMember();
		$region = $this->_getTable('RegionTable');
		$prov[0] = $cityList[0] = $districtList[0] = '请选择';
		$prov += $region->getSelectRegion(2);
		$cityList += $region->getSelectRegion(3, $userInfo['Province']);
		$districtList += $region->getSelectRegion(4, $userInfo['City']);
		$req = $this->getRequest();
		if ($req->isPost()) {
			$params = $req->getPost();
			$update = (array)$params;
			if (isset($params['tel1']) && isset($params['tel2']) && isset($params['tel3'])) {
				$update['Tel'] = $params['tel1'].'-'.$params['tel2'].'-'.$params['tel3'];
			}
			if (isset($params['birth1']) && isset($params['birth2']) && isset($params['birth3'])) {
				$update['Birthday'] = $params['birth1'].'-'.$params['birth2'].'-'.$params['birth3'];
			}
			$member = $this->_getTable('MemberTable');
			$flg = $member->updateFieldsByID($update, $userInfo['UserID']);
			if ($flg) {
				$this->_message('修改成功！', 'success');
			} else {
				$this->_message('修改成功！', 'success');
			}
			return $this->redirect()->toUrl('/member');
		}
		$assign = array(
			'user' => $userInfo,
			'prov' => $prov,
			'cityList' => $cityList,
			'districtList' => $districtList
		);
		return new ViewModel($assign);
	}
	public function addressAction()
	{
		$this->_isCenter();
	}
	public function showAddressAction()
	{
		$this->_isCenter();
		$id = $this->params()->fromQuery('id');
		$region = $this->_getTable('RegionTable');
		$prov[0] = $cityList[0] = $districtList[0] = '请选择';
		$prov += $region->getSelectRegion(2);
		$addr = array();
		if ($id) {
			$address = $this->_getTable('AddressTable');
			$userInfo = $this->_currentMember();
			$user_id = $userInfo['UserID'];
			$addr = $address->getAddressInfoById($user_id, $id);
			if ($addr->province) {
				$cityList += $region->getSelectRegion(3, $addr->province);
			}
			if ($addr->city) {
				$districtList += $region->getSelectRegion(4, $addr->city);
			}
		}
		$assign = array(
			'prov' => $prov,
			'city' => $cityList,
			'district' => $districtList,
			'address' => $addr,
			'defaultAddress' => isset($userInfo['address_id']) ? $userInfo['address_id'] : 0,
		);
		$v = new ViewModel($assign);
		$v->setTerminal(true);
// 		$v->setTemplate('front-end/member/show-address.phtml');
		return $v;
	}
	public function updateAddressAction()
	{
		$this->_isCenter();
		$req = $this->getRequest();
		if ($req->isPost()) {
			$params = $req->getPost();
// 			print_r($params);die;
			$update = (array)$params;
			$update['user_id'] = $this->_getCurrentUserID();
			$update['tel'] = $params->tel1.'-'.$params->tel2.'-'.$params->tel3;
			$address = $this->_getTable('AddressTable');
			if ($params->id) {
				$curID = $params->id;
				$address->updateFieldsByID($update, $params->id);
				$member = $this->_getTable('MemberTable');
				$this->_message('修改成功！', 'success');
			} elseif ($address->checkAddrCount($update['user_id'])) {
				$update['addTime'] = date('Y-m-d H:i:s');
				$address->insertAddress($update);
				$curID = $address->getLastInsertValue();
				$this->_message('添加成功！', 'success');
			} else {
				$this->_message('抱歉，您的收货地址已超过20条,不能继续添加了', 'error');
				return $this->redirect()->toUrl('/member/address');
			}
			if (isset($params->firstaddress) && $params->firstaddress) {
				$member = $this->_getTable('MemberTable');
				$member->updateFieldsByID(array('address_id' => $curID), $update['user_id']);
			}
			return $this->redirect()->toUrl('/member/address');
		}
	}
	public function setDefaultAction()
	{
		$this->_isCenter();
		$assign = array();
		$id = $this->params()->fromQuery('id');
		if (!$id) {
			throw new \Exception('id is must!');
		}
		$member = $this->_getTable('MemberTable');
		$user_id = $this->_getCurrentUserID();
		if ($member->updateFieldsByID(array('address_id' => $id), $user_id)) {
			$this->_message('设置成功！', 'success');
			$assign = array('code' => 0, 'msg' => '设置成功！');
		} else {
			$this->_message('设置失败！', 'error');
			$assign = array('code' => -1, 'msg' => '设置失败！');
		}
		return new JsonModel($assign);
	}
	public function addressListAction()
	{
		$this->_isCenter();
		$userInfo = $this->_currentMember();
		$userID = $userInfo['UserID'];
		$address = $this->_getTable('AddressTable');
		$addressList = $address->addressListByUser($userID);
		$addressList = $addressList->toArray();
		$addr = array();
		if ($addressList) {
			$region = $this->_getTable('RegionTable');
			foreach ($addressList as $k=>$v) {
				$data = $region->getAreaNameByIDs($v['province'], $v['city'], $v['district']);
				$addressList[$k]['area'] =  isset($data['address']) ? $data['address'] : '';
				$addressList[$k]['default'] = $userInfo['address_id'] == $v['id'] ? 1 : 0;
			}
		}
		$assign = array(
			'addressList' => $addressList,
		);
		$v = new ViewModel($assign);
		$v->setTerminal(true);
		return $v;
	}
	public function delAddressAction()
	{
		$this->_isCenter();
		$assign = array();
		$id = $this->params()->fromQuery('id');
		if (!$id) {
			throw new \Exception('id is must!');
		}
		$address = $this->_getTable('AddressTable');
		$user_id = $this->_getCurrentUserID();
		if ($address->delete(array('id' => $id))) {
			$this->_message('删除成功！', 'success');
			$assign = array('code' => 0, 'msg' => '删除成功！');
		} else {
			$this->_message('删除失败！', 'error');
			$assign = array('code' => -1, 'msg' => '删除失败！');
		}
		$v = new JsonModel($assign);
		return $v;
	}
	public function checkInfoAction()
	{
		$this->_isCenter();
		$req = $this->getRequest();
		$type = $this->params()->fromPost('type');
		if ($req->isPost() && $type == 'save') {
			$msg = $this->params()->fromPost('msg');
			$userid = $this->_getCurrentUserID();
			$member = $this->_getTable('MemberTable');
			if ($member->updateFieldsByID(array('leftMsg' => $msg), $userid)) {
				$this->_message('设置成功！', 'success');
				$assign = array('code' => 0, 'msg' => '设置成功！');
			} else {
				$this->_message('设置失败！', 'error');
				$assign = array('code' => -1, 'msg' => '设置失败！');
			}
			$v = new JsonModel($assign);
			return $v;
			
		}
		$userInfo = $this->_currentMember();
		$assign = array(
			'user' => $userInfo
		);
		return new ViewModel($assign);
	}
	public function passwordAction()
	{
		$this->_isCenter();
		$req = $this->getRequest();
		if ($req->isPost()) {
			$params = $req->getPost();
			if (!$params->password) {
				$this->_message('旧密码不能为空!', 'error');
				return $this->redirect()->toUrl('/member/password');
			}
			if ($params->valiate_code != $_SESSION['modify_pass_captcha_code']) {
				$this->_message('验证码错误，或验证码已过期！', 'error');
				return $this->redirect()->toUrl('/member/password');
			}
			if ($params->newpassword != $params->renewpassword) {
				$this->_message('新密码两次输入不一致！', 'error');
				return $this->redirect()->toUrl('/member/password');
			}
			$member = $this->_getTable('MemberTable');
			$userid = $this->_getCurrentUserID();
			if (!$this->_chkPassword($params->password, $userid)) {
				$this->_message('旧密码不正确！', 'error');
				return $this->redirect()->toUrl('/member/password');
			}
			$flg = $member->updateFieldsByID(array('Password' => md5($params->newpassword)), $userid);
			if ($flg) {
				$this->_message('恭喜您,密码修改成功！', 'success');
			} else {
				$this->_message('抱歉，密码修改失败！', 'error');
			}
			return $this->redirect()->toUrl('/member/password');
		}
	}
	private function _chkPassword($password, $uid)
	{
		$member = $this->_getTable('MemberTable');
		$user = $member->getOneForId($uid);
		return $user->Password == md5($password) ? true : false;
	}
	public function emailAction()
	{
		$this->_isCenter();
		$member = $this->_getTable('MemberTable');
		$userid = $this->_getCurrentUserID();
		$user = $member->getOneForId($userid);
		
		$req = $this->getRequest();
		if ($req->isPost()) {
			$params = $req->getPost();
			if (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$/', $params->toEmail)) {
				$this->_message('邮箱格式不正确', 'error');
				return $this->redirect()->toUrl('/member/email');
			}
			if ($params->valicate_code != $_SESSION['chk-email-captcha']) {
				$this->_message('抱歉，验证码不正确，或者验证码已经过期', 'error');
				return $this->redirect()->toUrl('/member/email');
			}
			if ($member->checkExist(array('Email' =>$params->toEmail), $userid)) {
				$this->_message('抱歉,邮箱地址'.$params->toEmail.'已被注册过，请更换其他可用邮箱', 'error');
				return $this->redirect()->toUrl('/member/email');
			}
			$code = $userid.'||'.$user->Email.'||'.$params->toEmail.'||'.time();
			$k = md5($user->Email);
			$code = base64_encode($code); 
			$url = Utilities::get_domain()."/member/chkUpdateEmail?uid={$userid}&code=".base64_encode($code)."&key=".$k;
			$content = ConfigTable::getSysConf('emailActTemplate');
			$content = str_replace(array('{userName}', '{email}', '{url}'), array($user->UserName, $params->toEmail, $url), $content);
			$mail = new \FrontEnd\Model\System\Mail();
			$mail->sendHtml($params->toEmail, '邮箱地址验证激活', $content);
			$this->_message('验证邮件已发送至'.$params->toEmail.'请注意查收', 'success');
			return $this->redirect()->toUrl('/member/email');
		}
		
		return array('user' => $user);
		
	}
	public function chkUpdateEmailAction()
	{
		$uid = $this->params()->fromQuery('uid' , '');
		$code = $this->params()->fromQuery('code' , '');
		$key = $this->params()->fromQuery('key' , '');
		$memberTable = $this->_getTable('MemberTable');
		$memberInfo = $memberTable->getOneById($uid);
		$str = base64_decode(base64_decode($code));
		$r = explode("||", $str);//14||yofas@foxmail.com||329036618@qq.com||1383224435
		if (isset($r[3]) && time()-$r[3] > 48*60*60) {
			header('Content-Type: text/html; charset=utf-8');
			die('抱歉，验证链接已过期！');
		}
		if (isset($r[0]) && isset($r[1]) && $r[0] == $memberInfo->UserID && $r[1] == $memberInfo->Email && md5($memberInfo->Email) == $key) {
			$memberTable->update(array('Email' => $r[2],'isValidEmail' => 1), array('UserID' => $uid));
			$this->layout('layout/account');
			$memberInfo->Email = $r[2];
			return new ViewModel(array('user' => $memberInfo));
		} else {
			header('Content-Type: text/html; charset=utf-8');
			die('验证失败，或者地址已失效');
		}
		
	}
	public function mobileAction()
	{
		$this->_isCenter();
	}
	public function identityAction()
	{
		$this->_isCenter();
		$member = $this->_getTable('MemberTable');
		$identity = $this->_getTable('IdentityTable');
		$userid = $this->_getCurrentUserID();
		$user = $member->getOneForId($userid);
		$req = $this->getRequest();
		$assign = array('identityInfo'=> '');
		$identityInfo = $identity->getOneByUID($userid);
		$assign['identityInfo'] = $identityInfo;
		if ($req->isPost()) {
			$params = $req->getPost();
			$name = $params->textfield2;
			$code = $params->textfield4;
			$type = $params->type;
			
			$data = array(
				'name' => $name,
				'code' => $code,
				'type' => $type,
			);
			if (!$identity->checkExist(array('user_id'=>$userid))) {
				$data['user_id'] = $userid;
				$data['addTime'] = date('Y-m-d H:i:s');
				$identity->insert($data);
			} else {
				$data['status'] = 0;
				$identity->update($data, array('user_id' => $userid));
			}
			$identityInfo = $identity->getOneByUID($userid);
			$assign['identityInfo'] = $identityInfo;
			$assign['applied'] = 1;
		}
		
		$assign['user'] = $user;
		return new ViewModel($assign);
	}
	public function secretAction()
	{
		$this->_isCenter();
	}
	public function findPasswordAction()
	{
		$this->layout('layout/account');
		$req = $this->getRequest();
		if ($req->isPost()) {
			$params = $req->getPost();
			if (isset($params['UserName']) && !empty($params['UserName'])) {
// 				$captcha_sess = $this->_getSession('Zend_Form_Captcha_'.$_SESSION['forget_captcha_code']);
				if (strtolower($_SESSION['forget_captcha_code']) == strtolower($params['validCode'])) {
					$member = $this->_getTable('MemberTable');
					$memberInfo = $member->getUserByUserName($params['UserName']);
					return array('memberInfo' => $memberInfo);
				}
			}
		}
// 		$member = $this->_getTable('MemberTable');
// 		$memberInfo = $member->getUserByUserName('xml');
// 		return array('memberInfo' => $memberInfo);
		return array();
	}
	public function confirmAction()
	{
		$this->layout('layout/account');
		$req = $this->getRequest();
		
		$userId = $this->params()->fromQuery('uid');
		$actKey = $this->params()->fromQuery('actKey');
		$step = $this->params()->fromQuery('s');
		$pass = substr($actKey,0, 32);
		$code = substr($actKey, 32, strlen($actKey));
		$chk = 1;
		if (time() - base64_decode($code) > 72*60*60) {
			header('Content-Type: text/html; charset=utf-8');
			die('链接地址已失效！');
		}
		$member = $this->_getTable('MemberTable');
		$memberInfo = $member->getOneById($userId);
		if ($pass != md5($memberInfo->Password)) {
			header('Content-Type: text/html; charset=utf-8');
			die('抱歉，链接地址已失效！');
		}
		$url = Utilities::get_domain()."/forget-confirm.do?uid={$userId}&actKey=".$actKey;
		
		if ($req->isPost()) {
			$params = $req->getPost();
			//validation reg
			
			$assign = array();
			$chk = 1;
			if (!(isset($params['password']) && isset($params['repassword']))) {
				$chk = 0;
				$assign = array('code' => -1, 'msg' => '参数错误');
			} else {
				if (!(preg_match('/^.{6,16}$/', $params['password']) && preg_match('/^.{6,16}$/', $params['repassword']))) {
					$chk = 0;
					$assign = array('code' => -2, 'msg' => '密码必须为6到16位字符');
				}  elseif (preg_match('/^[0-9]+$/', $params['password']) || preg_match('/^[0-9]+$/', $params['repassword']) || preg_match('/^[a-zA-Z]+$/', $params['password']) || preg_match('/^[a-zA-Z]+$/', $params['repassword'])) {
					$chk = 0;
					$assign = array('code' => -3, 'msg' => '密码不能为纯数字或纯字母组成');
				}
			}
			if ($chk) {
				if ($params['password'] == $params['repassword']) {
					$flg = $member->update(array('Password' => md5($params['password'])), array('UserID' => $memberInfo->UserID));
					if ($flg) {
						$assign = array('code' => 0, 'msg' => '恭喜，密码重置成功！');
					} else {
						$assign =  array('code' => 1, 'msg' => '抱歉，密码重设失败！');
					}
				} else {
					$assign = array('code' => 2, 'msg' => '两次密码输入不一致！');
				}
			}
			
			$v = new ViewModel($assign);
			$v->setTemplate('front-end/member/reset');
			return $v;
		}
		if ($step == 'next') {
			return array('url', 'user' => $memberInfo);
		}
		return array('url' => $url);
	}
	function captchaAction()
	{
		unset($_SESSION['forget_captcha_code']);
		$captcha = new \Custom\Captcha\Image(array(
				'Expiration' => '300',
				'wordlen' => '4',
				'Height' => '28',
				'Width' => '77',
				'writeInFile'=>false,
				'Font' => APPLICATION_PATH.'/data/AdobeSongStd-Light.otf',
				'FontSize' => '22',
				'DotNoiseLevel' => 0,
				'ImgDir' => '/images/FrontEnd'
		));
		$imgName = $captcha->generate();
		$_SESSION['forget_captcha_code'] = $captcha->getWord();
		die;
	}
	function chkCaptchaAction()
	{
		$k = $this->params()->fromQuery('k');
		
		unset($_SESSION[$k]);
		$captcha = new \Custom\Captcha\Image(array(
				'Expiration' => '300',
				'wordlen' => '4',
				'Height' => '21',
				'Width' => '55',
				'writeInFile'=>false,
				'Font' => APPLICATION_PATH.'/data/AdobeSongStd-Light.otf',
				'FontSize' => '22',
				'DotNoiseLevel' => 0,
				'ImgDir' => '/images/FrontEnd'
		));
		$imgName = $captcha->generate();
		$_SESSION[$k] = $captcha->getWord();
		die;
	}
	private function _getMemberByID($UserID)
	{
		$table = $this->_getTable('MemberTable');
		$rs = $table->getOneForId($UserID);
		
		return $rs->toArray();
	}
	private function _currentMember()
	{
		$container = $this->_getSession('member');
		return $this->_getMemberByID($container->UserID);
	}
	private function _getCurrentUserID()
	{
		$container = $this->_getSession('member');
		return $container->UserID;
	}
	private function _insertImg($name)
	{
		$config = $this->_getConfig('upload');
		$config = $config['member'];
		$files = $this->params()->fromFiles('ImgUrl');
		if(! empty($files['name'])){
			return $config['showPath'] . Uploader::upload($files , $name , $config['uploadPath'] , $config);
		}else{
			return $this->params()->fromPost('ImgUrl');
		}
	
		return null;
	}
	private function _updateMemberImage($UserId , $imageFile){
		$memberTable = $this->_getTable('memberTable');
		return $memberTable->updateImage($UserId , $imageFile);
	}
	private function _login()
	{
		$container = $this->_getSession('member');
		if (!isset($container->UserID)) {
			return $this->redirect()->toUrl('/login');
		}
	}
	private function _isCenter()
	{
		$this->_login();
		$this->layout('layout/center');
	}
}