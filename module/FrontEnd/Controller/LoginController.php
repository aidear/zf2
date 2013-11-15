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
					setcookie('userName', $userName);
				} else {
					setcookie('userName', '', time()-1000);
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
    					$container->LoginCount = $user->LoginCount+1;
    					$container->Points = $user->Points;
						$rs = array('code' => 0, 'msg' => '登录成功');
					} else {
						$rs = array('code' => 1, 'msg' => '密码输入有误');
					}
				} else {
					$rs = array('code' => 2, 'msg' => '用户名不存在');
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