<?php
namespace FrontEnd\Controller;

use Zend\Session\Container;

use Zend\Db\TableGateway\TableGateway;

use Zend\Db\ResultSet\ResultSet;


use Custom\Mvc\Controller\AbstractActionController;
use Zend\View\Helper\ViewModel;
use FrontEnd\Model\Users\Member;
use FrontEnd\Model\Users\MemberTable;
use Zend\View\Model\JsonModel;

class LoginController extends AbstractActionController
{
	public function indexAction()
	{
// 		$url = $this->params()->fromQuery('url' , '');
		$this->layout('layout/login');
		
		$req = $this->getRequest();
		if ($req->isPost()) {
			$rs = array();
			$userName = $this->params()->fromPost('username');
			$password = $this->params()->fromPost('password');
			$rememberMe = $this->params()->fromPost('rememberMe');
			if ($userName && $password) {
				$memberTable = $this->_getTable('MemberTable');
				$user = $memberTable->getUserByUserName($userName);
				if ($rememberMe) {
					$_COOKIE['userName'] = $userName;
				} else {
					unset($_COOKIE['userName']);
				}
				if ($user) {
					if ($user->Password == md5($password)) {
						$now = date('Y-m-d H:i:s');
						$sql = "UPDATE member SET LoginCount=LoginCount+1, LastLogin='{$now}', LastUpdate='{$now}' WHERE UserID={$user->UserID}";
						$statement = $memberTable->getAdapter()->query($sql);
						$statement->execute();
// 						$memberTable->update(array('LoginCount' => 'LoginCount+1', 'LastLogin' => $now, 'LastUpdate' => $now), array('UserID' => $user->UserID));
						$container = $this->_getSession('member');
						$container->UserID = $user->UserID;
    					$container->UserName = $user->UserName;
						$rs = array('code' => 0, 'msg' => '登录成功');
					} else {
						$rs = array('code' => 1, 'msg' => '密码错误！');
					}
				} else {
					$rs = array('code' => 2, 'msg' => '帐号不存在！');
				}
			} else {
				$rs = array('code' => -1, 'msg' => '请提供用户名和密码');
			}
			
			$v = new JsonModel($rs);
			$v->setTerminal(true);
			return $v;
		}
		
		return array();
	}
	function submitAction(){
		$request = $this->request;
		if($this->request->isPost()){
			$username = $this->params()->fromPost('username');
			$pwd = $this->params()->fromPost('password');
			$data = $request->getPost();
			$form = new LoginForm();
			$form->setData($data);
			if($username && $pwd){
				$sm = $this->getServiceLocator();
				$userTable = $sm->get('UserTable');
				$user = $userTable->getUserByName($username);
				
				if ($user && $user->Password == md5($pwd)) {
					$container = $this->_getSession('member');
					$container->UserID = $user->ID;
					$container->Name = $user->UserName;
// 					$container->LastChangeDate = $user->LastChangeDate;
					$container->Role = $user->Role;
					if($url = $this->params()->fromQuery('url')){
						return $this->redirect()->toUrl($url);
					}else{
						return $this->redirect()->toRoute('home');
					}
				} elseif ($user) {
// 					$this->flashMessenger()->addMessage('用户名或密码错误');
					$this->_message('用户名或密码错误', 'error');
					return $this->redirect()->toUrl('/login');
				} else {
// 					$this->flashMessenger()->addMessage('没有这个用户:' . $username);
					$this->_message('没有这个用户:' . $username, 'error');
					return $this->redirect()->toUrl('/login');
				}
			}else{
				throw new \Exception('error');
// 				$this->flashMessenger()->addMessage('用户名或密码不能为空');
				$this->_message('用户名或密码不能为空', 'error');
				return $this->redirect()->toUrl('/login');
			}
		}else{
// 			$this->flashMessenger()->addMessage('用户名或密码错误');
			$this->_message('用户名或密码错误', 'error');
			return $this->redirect()->toUrl('/login');
		}
		return $this->redirect()->toUrl('/login');
	}
	
	/**
	 * 登出
	 */
	public function logoutAction(){
		$container = $this->_getSession('member');
		$container->getManager()->destroy();
		return $this->redirect()->toUrl('/');
// 		return $this->redirect()->toRoute('frontend' , array('controller' => 'index' , 'action' => 'index'));
	}
}