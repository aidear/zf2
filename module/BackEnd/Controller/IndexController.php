<?php
namespace BackEnd\Controller;

use Custom\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
{
	protected $adminTable;
	public function indexAction()
	{
		$memberTable = $this->_getTable('MemberTable');
		$memberS = $memberTable->getStatistics();
		
		$rs = array('memberStatist' => $memberS);
// 		return $this->redirect()->toUrl('/login');
		return new ViewModel($rs);
	}
	public function getAdminTable()
	{
		if (!$this->adminTable) {
			$sm = $this->getServiceLocator();
			$this->adminTable = $sm->get('BackEnd\Model\AdminTable');
		}
		return $this->adminTable;
	}
	public function updateColumnAction()
	{
		$assign = array('code'=>0);
		$field = $this->params()->fromQuery('field');
		$primary_key = $this->params()->fromQuery('primary_key');
		$primary_value = $this->params()->fromQuery('primary_value');
		$tableModel = $this->params()->fromQuery('table');
		$value = $this->params()->fromQuery('value');
		
		$table = $this->_getTable($tableModel);
		$update[$field] = $value;
		$where[$primary_key] = $primary_value;
		
		if ($tableModel == 'MemberTable' && $field == 'UserName' && $table->checkExist(array('UserName' => $value), $primary_value)) {
			$assign = array('code' => 1, 'msg' => "用户名{$value}重复");
		} elseif($tableModel == 'MemberTable' && $field == 'Email' && $table->checkExist(array('Email' => $value), $primary_value)) {
			$assign = array('code' => 1, 'msg' => "邮箱{$value}重复");
		} elseif($tableModel == 'MemberTable' && $field == 'Mobile' && $table->checkExist(array('Mobile' => $value), $primary_value)) {
			$assign = array('code' => 1, 'msg' => "手机号码{$value}重复");
		} else {
			if($tableModel == 'IdentityTable' && $field == 'status' && $value==1) {
				$update['lastApproved'] = date('Y-m-d H:i:s');
			}
			$flg = $table->update($update, $where);
			if ($flg) {
				$assign = array('code' => 0, 'msg' => 'success');
			} else {
				$assign = array('code' => -1, 'msg' => 'fail');
			}
		}
		
		$v = new JsonModel($assign);
		$v->setTerminal(true);
		return $v;
	}
}