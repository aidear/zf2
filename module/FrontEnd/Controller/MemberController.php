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
		if ($step == 'next') {
			return array('url', 'user' => $memberInfo);
		}
		return array('url' => $url);
	}
	function captchaAction()
	{
		$captcha = new \Zend\Captcha\Image(array(
				'Expiration' => '300',
				'wordlen' => '4',
				'Height' => '28',
				'Width' => '77',
				'writeInFile'=>false,
				'Font' => APPLICATION_PATH.'/data/AdobeSongStd-Light.otf',
				'FontSize' => '24',
				'DotNoiseLevel' => 8,
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