<?php
namespace FrontEnd\Controller;


use Custom\Mvc\Controller\AbstractActionController;
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
    					$container->LoginTime = time();
    					
    					//check promotion type login:
    					$phistoryTable = $this->_getTable('PointHistoryTable');
    					$curTotalPoints = $phistoryTable->getTotalPointsCurDay($user->UserID);
    					if ($curTotalPoints < 5) {
    						$promotionTable = $this->_getTable('PromotionTable');
    						$loginProList = $promotionTable->getProList('login');
    						if ($loginProList) {
    							foreach ($loginProList as $k=>$v) {
    								$hisInsert = array(
    										'uid' => $user->UserID,
    										'rule_id' => $v['id'],
    										'rule_name' => $v['type_name'],
    										'points' => $v['points'],
    										'info' => NULL,
    										'description' => '登录赠送'.$v['points'].'积分',
    										'add_time' => $now,
    										'record_type' => 1//系统
    								);
    								$memberTable->updateUserPoint($user->UserID, $v['points'], $hisInsert);
    							}
    						}
    					}
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
		$diff = time()-$container->LoginTime;
		$hours = floor($diff/3600);
		if ($hours >= 1) {
			$promotionTable = $this->_getTable('PromotionTable');
			$onlineProList = $promotionTable->getProList('online');
			if ($onlineProList) {
				$memberTable = $this->_getTable('MemberTable');
				$now = date('Y-m-d H:i:s');
				foreach ($onlineProList as $k=>$v) {
					$points = $hours*$v['points'];
					$hisInsert = array(
							'uid' => $container->UserID,
							'rule_id' => $v['id'],
							'rule_name' => $v['type_name'],
							'points' => $points,
							'info' => NULL,
							'description' => '在线时长满'.$hours.'小时，赠送'.$points.'积分',
							'add_time' => $now,
							'record_type' => 1//系统
					);
					$memberTable->updateUserPoint($container->UserID, $points, $hisInsert);
				}
			}
		}
		$container->getManager()->destroy();
		return $this->redirect()->toUrl('/');
// 		return $this->redirect()->toRoute('frontend' , array('controller' => 'index' , 'action' => 'index'));
	}
}