<?php
namespace BackEnd\Controller;

use Zend\Session\Container;

use Zend\Db\TableGateway\TableGateway;

use Zend\Db\ResultSet\ResultSet;

use BackEnd\Form\LoginForm;

use Custom\Mvc\Controller\AbstractActionController;
use Zend\View\Helper\ViewModel;
use BackEnd\Model\Users\User;
use BackEnd\Model\Users\UserTable;

class LoginController extends AbstractActionController
{
	public function indexAction()
	{
		$url = $this->params()->fromQuery('url' , '');
		$form = new LoginForm();
		$this->layout('layout/basic');
		
		$return = array('url' => $url , 'form' => $form);
// 		if($this->flashMessenger()->hasMessages()){
// 			$return['msg'] = $this->flashMessenger()->getMessages();
// 		}
		return $return;
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
					$container = $this->_getSession();
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
		$container = new Container('user');
		$container->getManager()->destroy();
		return $this->redirect()->toRoute('backend' , array('controller' => 'login' , 'action' => 'index'));
	}
}