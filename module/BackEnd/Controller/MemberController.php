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
namespace BackEnd\Controller;

use Zend\Paginator\Paginator;
use Zend\Form\Form;
use Zend\Form\Element;
use Custom\Mvc\Controller\AbstractActionController;
use Custom\Util\Utilities;
use BackEnd\Model\Users\Member;
use BackEnd\Model\Users\MemberTable;
use BackEnd\Form\MemberForm;
use BackEnd\Model\Users\RegionTable;
use Custom\Mvc\ActionEvent;
use Custom\File\Uploader;
use Zend\View\Model\ViewModel;
use Custom\Validator\UserNameUnipue;

use Zend\Validator\File\Size;

use Zend\File\Transfer\Adapter\Http;

class MemberController extends AbstractActionController
{
	public function indexAction()
	{
		$routeParams = array('controller' => 'member' , 'action' => 'index');
		$prefixUrl = $this->url()->fromRoute(null, $routeParams);
		$params = array();
		
        $table = $this->_getTable('MemberTable');
        $k = $this->params()->fromQuery('k' , '');
        
        if($k){
        	$params['k']  = $k;
        }
        $params['orderField'] = $this->params()->fromQuery('orderField', '');
        $params['orderType'] = $this->params()->fromQuery('orderType', '');
        if ($this->params()->fromQuery('pageSize')) {
        	$params['pageSize'] = $this->params()->fromQuery('pageSize');
        }
        
        $removePageParams = $params;
        
        $params['page'] = $this->params()->fromQuery('page' , 1);
        
        $orderPageParams = $params;
        
        $paginaction = $this->_getNavPaginator($params);
        
        $startNumber = 1+($params['page']-1)*$paginaction->getItemCountPerPage();
        $order = $this->_getOrder($prefixUrl, array('UserName', 'Points', 'Email', 'Mobile', 'Nick', 'LastLogin', 'LoginCount', 'LastUpdate'), $removePageParams);
        
        $assign = array(
        		'paginaction' => $paginaction, 
        		'startNumber' => $startNumber,
        		'orderQuery' => http_build_query($orderPageParams),
        		'query' => http_build_query($removePageParams),
        		'order' => $order,
        );
        return new ViewModel($assign);
	}
	private function _getNavPaginator($params)
	{
		$page = isset($params['page']) ? $params['page'] : 1;
		$order = array();
		if ($params['orderField']) {
			$order = array($params['orderField'] => $params['orderType']);
		}
		$table = $this->_getTable('MemberTable');
		$paginator = new Paginator($table->formatWhere($params)->getListToPaginator($order));
		$paginator->setCurrentPageNumber($page)->setItemCountPerPage(isset($params['pageSize']) ? $params['pageSize'] : self::LIMIT);
		return $paginator;
	}
	public function saveAction()
	{
		$requery = $this->getRequest();
		$form = new MemberForm($this->_getTable('Zend\Db\Adapter\Adapter'));
		if($requery->isPost()){
			$params = $requery->getPost();
			$member = new Member($this->_getTable('Zend\Db\Adapter\Adapter'));
			
			$member->UserID = $params->UserID;
			$member->UserName = $params->UserName;
// 			$member->Password = md5($params->Password);
			$member->Nick = $params->Nick;
// 			$member->ImgUrl = $params->ImgUrl;
			$member->Email = $params->Email;
			$member->Mobile = $params->Mobile;
			$member->Points = $params->Points;
			$member->TrueName = $params->TrueName;
			$member->Gender = $params->Gender;
			$member->Province = $params->Province;
			$member->City = $params->City;
			$member->District = $params->District;
			$member->Address = $params->Address;
			$member->Tel = $params->Tel;
			$member->Birthday = $params->Birthday;
			$member->QQ = $params->QQ;
			$member->MSN = $params->MSN;
			$member->Status = $params->Status;
			$member->Source = $params->Source;
			$member->LastUpdate = $member->AddTime = Utilities::getDateTime();
			$form->setData($params);
			
			$form->setInputFilter($member->getInputFilter());
				
			if ($form->isValid()) {
// 				$member->exchangeArray($form->getData());
				$table = $this->_getTable('MemberTable');
				$chkExist = 1;
// 				if ($table->checkExist(array('UserName' => $params->UserName), $params->UserID)) {
// 					$chkExist = 0;
// 					$this->flashMessenger()->addErrorMessage("用户名:{$params->UserName}已被占用！请更换其他用户名");
// 				}
// 				if ($table->checkExist(array('Email' => $params->Email), $params->UserID)) {
// 					$chkExist = 0;
// 					$this->flashMessenger()->addErrorMessage("邮箱:{$params->Email}已被占用！请更换其他邮箱");
// 				}
// 				if ($params->Mobile && $table->checkExist(array('Mobile' => $params->Mobile), $params->UserID)) {
// 					$chkExist = 0;
// 					$this->flashMessenger()->addErrorMessage("手机:{$params->Mobile}已被占用！请更换其他手机号码");
// 				}
// 				if (!$chkExist) {
// 					if ($params->UserID) {
// 						return $this->redirect()->toUrl("/member/save?id={$params->UserID}");
// 					} else {
// 						return $this->redirect()->toRoute('backend' , array('controller' => 'member' , 'action' => 'save'));
// 					}
// 				} 
				
				$id = $table->save($member);
			
				//插入图片
				$member->ImgUrl = $this->_insertImg($member->UserID ? $member->UserID : $id);
				//更新表
				if($member->ImgUrl){
					$this->_updateMemberImage($member->UserID ? $member->UserID : $id, $member->ImgUrl );
				}
				
				if($member->UserID){
					$this->trigger(ActionEvent::ACTION_UPDATE);
					$this->_message('用户名为'.$member->UserName.'的会员资料更新成功');
				}else{
					$this->_message('用户名为'.$member->UserName.'的会员添加成功');
					$this->trigger(ActionEvent::ACTION_INSERT);
				}
				return $this->redirect()->toRoute('backend' , array('controller' => 'member' , 'action' => 'index'));
			}
			
			
		} elseif ($UserID = $this->params()->fromQuery('id')) {
			$member = new Member();
			
			$userInfo = $this->_getMemberByID($UserID);
            $form->setData($userInfo);
		} else {
			
		}
		$region = $this->_getTable('RegionTable');
		$prov = $region->getSelectRegion(2);
		$provK = array_keys($prov);
		$dC = isset($userInfo['Province']) ? $userInfo['Province'] : array_shift($provK);
		$city = $region->getSelectRegion(3, $dC);
		$cityK = array_keys($city);
		$dDi = isset($userInfo['City']) ? $userInfo['City'] : array_shift($cityK);
		$district = $region->getSelectRegion(4, $dDi);
		$form->get('Province')->setValueOptions($prov);
		$form->get('City')->setValueOptions($city);
		$form->get('District')->setValueOptions($district);
		$form->get('Source')->setValue(2);
		
// 		if($this->flashMessenger()->hasMessages()){
// 			return array('form' => $form, 'msg' => $this->flashMessenger()->getMessages());
// 		} else {
			return array('member' => new Member() , 'form' => $form);
// 		}
	}
	
	function pwdAction()
	{
		$form = new MemberForm();
		$post = $this->getRequest();
		if($post->isPost()){
			$password = trim($post->getPost()->Password);
			$rePassword = trim($post->getPost()->rePassword);
			$UserID = $post->getPost()->UserID;
			if (empty($password) || empty($rePassword)) {
				$this->flashMessenger()->addErrorMessage('请输入密码后提交');
				
			} elseif ($password == $rePassword) {
				$memberTable = $this->_getTable('MemberTable');
				$memberTable->updateFieldsByID(array('Password' => md5($password)), $UserID);
				$this->flashMessenger()->addSuccessMessage('会员密码已重置成功');
// 				return $this->redirect()->toRoute('backend' , array('controller' => 'member' , 'action' => 'pwd'));
			} else {
				$this->flashMessenger()->addErrorMessage('抱歉，两次输入的密码不一致！请重新输入');
			}
			return $this->redirect()->toUrl("/member/pwd?id={$UserID}");
		}
		$UserID = $this->params()->fromQuery('id');
		$userInfo = $this->_getMemberByID($UserID);
		unset($userInfo['Password']);
		$form->setData($userInfo);
		$form->add ( array (
            'name' => 'rePassword',
            'options' => array (
                'label' => '确认密码' 
            ) 
        ) );
		
// 		if($this->flashMessenger()->hasMessages()){
// 			return array('form' => $form, 'msg' => $this->flashMessenger()->getMessages());
// 		} else {
			return array('form' => $form);
// 		}
		
// 		return array('form'=>'');
	}
	function sendMailAction()
	{
		$table = $this->_getTable('MemberTable');
		$userid = $id = $this->params()->fromQuery('id');
		if (!$userid) {
			throw new \Exception('id is required!');
		}
		$req = $this->getRequest();
		if ($req->isPost()) {
			$subject = $req->getPost('subject');
			$email = $req->getPost('email');
			$content = $req->getPost('content');
			$mail = new \BackEnd\Model\System\Mail();
			$email = explode(',', $email);
			$email = array_filter($email);
			$mail->sendHtml($email, $subject, $content);
			$this->_message('信息已投递至'.implode(',', $email).'地址');
			return $this->redirect()->toRoute('backend' , array('controller' => 'member' , 'action' => 'index')); 
		}
		if (strpos($userid, ',') !== false) {
			$userid = explode(',', $userid);
		}
		$user = $this->_getMemberByID($userid);
		$assign = array();
		if (count($user) != count($user,  COUNT_RECURSIVE)) {
			$assign['mult'] = 1;
			foreach ($user as $u) {
				$assign['email'][] = $u['Email'];
				$assign['name'][] = $u['UserName'];
			}
		} else {
			$assign['mult'] = 0;
			$assign['Email'] = $user['Email'];
			$assign['UserName'] = $user['UserName'];
		}
		
		return array('user' => $assign, 'id' => $id);
	}
	function deleteAction()
	{
		$requery = $this->getRequest();
		if($userId = $requery->getQuery('id')){
			$table = $this->_getTable('MemberTable');
			$table->delete($userId);
	
			$this->trigger(ActionEvent::ACTION_DELETE);
	
			$this->_message('删除成功');
			return $this->redirect()->toUrl('/member');
	
		}
		throw new \Exception('没有ID参数');
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