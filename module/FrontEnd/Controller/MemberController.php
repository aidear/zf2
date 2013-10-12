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

class MemberController extends AbstractActionController
{
	public function indexAction()
	{
		$page = $this->params()->fromQuery('page' , 1);
        $table = $this->_getTable('MemberTable');
        $username = $this->params()->fromQuery('username' , '');
        
        if($username){
            $re = $table->getUserForName($username);
        }else{
            $re = $table->getAllToPage();
        }
        
        $paginaction = new Paginator($re);
        $paginaction->setCurrentPageNumber($page);
        $paginaction->setItemCountPerPage(self::LIMIT);
        return array('paginaction' => $paginaction);	
	}
	public function findPasswordAction()
	{
		$this->layout('layout/account');
		$req = $this->getRequest();
		if ($req->isPost()) {
			$params = $req->getPost();
			if (isset($params['UserName']) && !empty($params['UserName'])) {
				$captcha_sess = $this->_getSession('Zend_Form_Captcha_'.$_SESSION['forget_captcha_code']);
				if ($captcha_sess->word == $params['validCode']) {
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
		$captcha = new \Zend\Captcha\Image(array(
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
		$_SESSION['forget_captcha_code'] = $imgName;
		die;
	}
	private function _getMemberByID($UserID)
	{
		$table = $this->_getTable('MemberTable');
		$rs = $table->getOneForId($UserID);
		
		return $rs->toArray();
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
}